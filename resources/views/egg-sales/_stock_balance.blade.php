@if(!empty($eggStock))
<span class="text-muted fw-normal fs-5">({{ number_format($eggStock['crates']) }} crates, {{ number_format($eggStock['loose']) }} eggs)</span>
@endif
