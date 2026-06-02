<?php

namespace App\Services;

use App\Models\EggSale;

class EggSaleBulkImportParser
{
    /**
     * @return array<int, array{
     *     buyer_name: string,
     *     buyer_contact: ?string,
     *     amount_paid: ?float,
     *     notes: ?string,
     *     items: array<int, array{
     *         egg_size: string,
     *         quantity: int,
     *         price_per_unit: float,
     *         payment_status: string,
     *         line_notes: ?string
     *     }>
     * }>
     */
    public function parse(string $text): array
    {
        $text = $this->normalizeText($text);

        $bracketClients = $this->parseBracketFormat($text);
        if (! empty($bracketClients)) {
            return $bracketClients;
        }

        return $this->parseReportFormat($text);
    }

    protected function normalizeText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/\\\\text\s*\{([^}]*)\}/', '$1', $text) ?? $text;
        $text = str_replace(['\\times', '×'], 'x', $text);

        $lines = explode("\n", $text);
        $lines = array_map(function (string $line) {
            return preg_replace('/\s+/', ' ', trim($line)) ?? trim($line);
        }, $lines);

        return trim(implode("\n", $lines));
    }

    /**
     * Bracket format:
     * [Client name | received: 703 | contact: 024...]
     * small, 10, 35, paid
     * medium, 4, 40, unpaid, Paid on 31-05-26
     */
    protected function parseBracketFormat(string $text): array
    {
        if (! preg_match('/\[[^\]]+\]/', $text)) {
            return [];
        }

        $clients = [];
        $blocks = preg_split('/(?=\[[^\]]+\])/m', $text) ?: [];

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '' || ! preg_match('/^\[([^\]]+)\](.*)$/s', $block, $headerMatch)) {
                continue;
            }

            $header = trim($headerMatch[1]);
            $body = trim($headerMatch[2]);
            $buyerName = $header;
            $amountPaid = null;
            $buyerContact = null;

            if (preg_match('/^([^|]+)\|(.+)$/s', $header, $parts)) {
                $buyerName = trim($parts[1]);
                $meta = $parts[2];
                if (preg_match('/received\s*:\s*([\d,]+(?:\.\d+)?)/i', $meta, $m)) {
                    $amountPaid = $this->parseAmount($m[1]);
                }
                if (preg_match('/contact\s*:\s*([^|]+)/i', $meta, $m)) {
                    $buyerContact = trim($m[1]);
                }
            }

            $items = [];
            foreach (preg_split('/\n/', $body) ?: [] as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                $item = $this->parseSimpleLine($line);
                if ($item !== null) {
                    $items[] = $item;
                }
            }

            if ($buyerName !== '' && ! empty($items)) {
                $clients[] = [
                    'buyer_name' => $buyerName,
                    'buyer_contact' => $buyerContact,
                    'amount_paid' => $amountPaid,
                    'notes' => null,
                    'items' => $items,
                ];
            }
        }

        return $clients;
    }

    protected function parseReportFormat(string $text): array
    {
        $lines = preg_split('/\n/', $text) ?: [];
        $clients = [];
        $current = null;

        foreach ($lines as $rawLine) {
            $line = trim($rawLine);
            if ($line === '') {
                continue;
            }

            if ($buyerName = $this->parseSectionHeader($line)) {
                if ($current !== null && ! empty($current['items'])) {
                    $clients[] = $this->finalizeClient($current);
                }
                $current = [
                    'buyer_name' => $buyerName,
                    'buyer_contact' => null,
                    'amount_paid' => null,
                    'notes' => null,
                    'items' => [],
                ];
                continue;
            }

            if ($current === null) {
                continue;
            }

            if ($item = $this->parseReportLineItem($line)) {
                $current['items'][] = $item;
                continue;
            }

            if ($amountPaid = $this->parseClientAmountPaid($line)) {
                $current['amount_paid'] = $amountPaid;
            }
        }

        if ($current !== null && ! empty($current['items'])) {
            $clients[] = $this->finalizeClient($current);
        }

        return $clients;
    }

    protected function parseSectionHeader(string $line): ?string
    {
        $patterns = [
            '/^#{1,4}\s*[A-Z]\.\s*(.+)$/i',
            '/^\*\s*[A-Z]\.\s*(.+)$/i',
            '/^[A-Z]\.\s*(.+)$/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line, $m)) {
                $buyerName = $this->cleanBuyerName($m[1]);
                return $buyerName !== '' ? $buyerName : null;
            }
        }

        return null;
    }

    protected function cleanBuyerName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace("/['']s\s+order$/i", '', $name) ?? $name;
        $name = preg_replace('/\s*\(egg seller\)$/i', '', $name) ?? $name;
        $name = trim($name, " \t.-");

        return trim($name);
    }

    protected function parseReportLineItem(string $line): ?array
    {
        $patterns = [
            '/^(?:\d+\.\s*)?(Small|Medium|Large|Big)\s+Eggs?\s*\D+(\d+)\s*x\s*(\d+(?:\.\d+)?)\s*=\s*[\d,]+(?:\s*GHS)?(?:\s*\(([^)]+)\))?/i',
            '/^(?:\d+\.\s*)?(Small|Medium|Large|Big)\s+Eggs?\s*\D+(\d+)\s*x\s*(\d+(?:\.\d+)?)/i',
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match($pattern, $line, $m)) {
                continue;
            }

            $paymentMeta = $m[4] ?? null;

            return [
                'egg_size' => $this->normalizeEggSize($m[1]),
                'quantity' => (int) $m[2],
                'price_per_unit' => (float) $m[3],
                'payment_status' => $this->parseLinePaymentStatus($paymentMeta),
                'line_notes' => $this->parseLineNotes($paymentMeta),
            ];
        }

        return null;
    }

    protected function parseSimpleLine(string $line): ?array
    {
        $parts = array_map('trim', explode(',', $line));
        if (count($parts) < 3) {
            return null;
        }

        $size = $this->normalizeEggSize($parts[0]);
        if (! in_array($size, [EggSale::SIZE_SMALL, EggSale::SIZE_MEDIUM, EggSale::SIZE_LARGE], true)) {
            return null;
        }

        if (! is_numeric($parts[1]) || ! is_numeric($parts[2])) {
            return null;
        }

        $paymentStatus = EggSale::PAYMENT_PAID;
        $lineNotes = null;

        if (isset($parts[3]) && $parts[3] !== '') {
            if (in_array(strtolower($parts[3]), ['paid', 'unpaid'], true)) {
                $paymentStatus = strtolower($parts[3]);
                $lineNotes = isset($parts[4]) ? trim(implode(',', array_slice($parts, 4))) : null;
            } else {
                $lineNotes = trim(implode(',', array_slice($parts, 3)));
                if (preg_match('/unpaid/i', $lineNotes)) {
                    $paymentStatus = EggSale::PAYMENT_UNPAID;
                }
            }
        }

        return [
            'egg_size' => $size,
            'quantity' => (int) $parts[1],
            'price_per_unit' => (float) $parts[2],
            'payment_status' => $paymentStatus,
            'line_notes' => $lineNotes !== '' ? $lineNotes : null,
        ];
    }

    protected function parseClientAmountPaid(string $line): ?float
    {
        $patterns = [
            '/payments?\s+received\s*:\s*([\d,]+(?:\.\d+)?)/i',
            '/subtotal\s*:\s*paid\s*:\s*([\d,]+(?:\.\d+)?)/i',
            '/^\*\s*paid\s*:\s*([\d,]+(?:\.\d+)?)/i',
            '/^paid\s*:\s*([\d,]+(?:\.\d+)?)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $line, $m)) {
                return $this->parseAmount($m[1]);
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $client
     * @return array<string, mixed>
     */
    protected function finalizeClient(array $client): array
    {
        if ($client['amount_paid'] === null) {
            $client['amount_paid'] = round(collect($client['items'])
                ->where('payment_status', EggSale::PAYMENT_PAID)
                ->sum(fn (array $item) => $item['quantity'] * $item['price_per_unit']), 2);
        }

        return $client;
    }

    protected function parseLinePaymentStatus(?string $meta): string
    {
        if ($meta === null || trim($meta) === '') {
            return EggSale::PAYMENT_PAID;
        }

        if (preg_match('/unpaid/i', $meta)) {
            return EggSale::PAYMENT_UNPAID;
        }

        return EggSale::PAYMENT_PAID;
    }

    protected function parseLineNotes(?string $meta): ?string
    {
        if ($meta === null) {
            return null;
        }

        $meta = trim($meta);
        if ($meta === '' || preg_match('/^(paid|unpaid)$/i', $meta)) {
            return null;
        }

        return $meta;
    }

    protected function normalizeEggSize(string $size): string
    {
        return match (strtolower(trim($size))) {
            'big', 'large' => EggSale::SIZE_LARGE,
            'medium' => EggSale::SIZE_MEDIUM,
            default => EggSale::SIZE_SMALL,
        };
    }

    protected function parseAmount(string $value): float
    {
        return (float) str_replace(',', '', trim($value));
    }
}
