@php
    $amounts = array_column($accounts, 'valor');
    $min = min([0, ...$amounts]);
    $max = max([0, ...$amounts]);
    $targetStep = max(1, ($max - $min) / 4);
    $magnitude = 10 ** floor(log10($targetStep));
    $step = ceil($targetStep / $magnitude) * $magnitude;
    $lowerBound = floor($min / $step) * $step;
    $upperBound = max($step, ceil($max / $step) * $step);
    $span = $upperBound - $lowerBound;
@endphp

@if($accounts)
    <figure class="income-chart">
        <figcaption>Ingresos por cuenta</figcaption>
        <div class="income-chart-scroll" tabindex="0" aria-label="Gráfico de ingresos por cuenta">
            <div class="income-chart-area" style="--account-count: {{ count($accounts) }}">
                <div class="income-chart-grid" aria-hidden="true">
                    @for($tick = $lowerBound; $tick <= $upperBound; $tick += $step)
                        <div class="income-chart-line {{ $tick == 0 ? 'chart-zero-line' : '' }}" style="--position: {{ 100 * ($tick - $lowerBound) / $span }}%">
                            <span>${{ number_format($tick, 0) }}</span>
                        </div>
                    @endfor
                </div>
                <ul class="income-chart-accounts">
                    @foreach($accounts as $account)
                        @php
                            $amount = $account['valor'];
                            $barStart = 100 * (min(0, $amount) - $lowerBound) / $span;
                        @endphp
                        <li class="income-chart-account">
                            <div class="income-chart-column" style="--bar-start: {{ $barStart }}%; --bar-size: {{ 100 * abs($amount) / $span }}%">
                                <span class="income-chart-bar {{ $amount < 0 ? 'chart-negative' : '' }}" aria-hidden="true"></span>
                                <span class="income-chart-amount">${{ number_format($amount, 2) }}</span>
                            </div>
                            <span class="income-chart-label">{{ $account['nombre'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </figure>
@else
    <p class="income-chart-empty">No hay cuentas de ingreso registradas.</p>
@endif
