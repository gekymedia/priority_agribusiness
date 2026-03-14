# AI Prompt: Structure Ledger Data for Bulk Add Expenses

Copy the prompt below and paste it into your AI assistant **together with your ledger or expense list**. The AI will return text in the exact format required by the **Bulk Add Expenses** feature so you can paste it into the app.

---

## Prompt to send to the AI

```
I have expense ledger entries that I need to paste into a "Bulk Add Expenses" form. Convert my list into this exact format:

**Output format**
- One line per expense.
- Columns separated by TAB (or comma):  date, description, amount, category
- Date: use a real date when I specify one (e.g. "21 Jan" → 21 Jan 2025, or keep as given). When the ledger says "—" or "same day" or the date is implied from the previous line, use "—" in the output so the app uses the default date.
- Description: the expense description as written (e.g. "Chicken feed", "Plastic bags", "Water tanker").
- Amount: numbers only, no currency symbol (e.g. 70, 260, 405).
- Category: pick ONE category from this exact list (use the name exactly as written):
  - Feed
  - Medication
  - Labor
  - Utilities
  - Equipment
  - Seeds
  - Fertilizer
  - Pesticides
  - Irrigation
  - Harvesting
  - Transportation
  - Administration
  - Maintenance

If a line doesn’t clearly fit any category, use "Administration" or "Equipment" as fallback.

**Example input (ledger):**
Date        Description                      Amount (GHS)
21 Jan      Transportation of water gallons  70
—          Bending wire                      60
—          Chicken feed                      260
—          Plastic bags                      100

**Example output (what I will paste into the app):**
21 Jan	Transportation of water gallons	70	Transportation
—	Bending wire	60	Equipment
—	Chicken feed	260	Feed
—	Plastic bags	100	Equipment

Now convert my expense list below into this format. Output only the converted lines (no header row, or use "Date	Description	Amount	Category" as first line if you want—the app will skip it).
```

Then paste your ledger (e.g. the Priority Agribusiness Ledger table) right after the prompt.

---

## Notes

- **Tab vs comma:** The app accepts both tab and comma as separators. Tab is preferred when copying from spreadsheets.
- **Default date:** On the Bulk Add page you choose a "Default date". Any line with "—" (or blank/same) in the date column will use that date.
- **Default category:** You also choose a default category on the form. The 4th column (category) is optional; if you omit it, every line uses that default. Including the category per line lets the AI assign the right one.
- **Header row:** If the first line looks like "Date", "Description", "Amount", it is skipped automatically.
