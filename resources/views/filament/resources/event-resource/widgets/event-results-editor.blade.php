<x-filament::widget>
    @if($record->date <= now())
        <x-filament::section>
            <x-slot name="heading">
                <div style="display:flex; align-items:center; gap:12px;">
                    <span>Výsledky</span>
                    @if($showShootout)
                        <span style="font-size:0.875rem; font-weight:600; color:#b45309;">🎯 Rozstřel</span>
                    @endif
                </div>
            </x-slot>

            <p style="font-size:0.9rem; color: #666; font-weight:400; margin-bottom: 1.5rem; margin-top: 0; padding: 0">Zadejte počet bodů pro každý tým. Rozstřel nastane automaticky, pokud dosáhlo nejvyššího skóre více týmů zároveň.</p>

            @if(count($rows) === 0)
                <p style="font-size:0.875rem; color:#6b7280;">Žádné registrované týmy.</p>
            @else

                {{-- Shootout panel — shown automatically when tied for 1st --}}
                @if($showShootout)
                    <div style="margin-bottom:1.5rem; border-radius:0.75rem; border:1px solid #f59e0b; background:rgba(255,251,235,0.8); padding:1rem;">
                        <p style="margin:0 0 0.75rem; font-size:0.875rem; font-weight:600; color:#92400e;">
                            🎯 Rozstřel — vyber vítěze
                        </p>

                        @foreach($tiedGroups as $group)
                            <div style="margin-bottom:0.5rem;">
                                <p style="margin:0 0 0.25rem; font-size:0.75rem; font-weight:500; color:#b45309; text-transform:uppercase; letter-spacing:0.05em;">
                                    {{ $group['teams'][0]['team_name'] }}
                                    @foreach(array_slice($group['teams'], 1) as $t)
                                        vs. {{ $t['team_name'] }}
                                    @endforeach
                                </p>
                                <select
                                    wire:model="shootoutWinners.{{ $group['position'] }}"
                                    style="max-width:24rem; width:100%; border-radius:0.5rem; border:1px solid #f59e0b; background:#fff; padding:0.375rem 0.75rem; font-size:0.875rem; color:#111827;"
                                >
                                    <option value="">— Vyber vítěze —</option>
                                    @foreach($group['teams'] as $team)
                                        <option value="{{ $team['team_id'] }}">{{ $team['team_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Table wrapper --}}
                <div style="width:100%; overflow-x:auto;">
                    <div style="min-width:280px;">

                        {{-- Column headers --}}
                        <div style="display:grid; grid-template-columns:2.25rem 1fr 6rem; gap:0.5rem; align-items:center; padding:0.25rem 0 0.5rem; border-bottom:2px solid rgba(0,0,0,0.1);">
                            <span style="font-size:0.7rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; text-align:center;">#</span>
                            <span style="font-size:0.7rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em;">Tým</span>
                            <span style="font-size:0.7rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; text-align:right;">Počet bodů</span>
                        </div>

                        {{-- Results rows --}}
                        @foreach($rows as $i => $row)
                            @php
                                $isTied = collect($tiedGroups)->contains(fn($g) => (int)$g['position'] === (int)$row['position']);
                                $wasShootout = in_array($row['team_id'], $shootoutParticipantIds);
                                $isWinner = $row['position'] !== '' && (int)$row['position'] === 1 && !$isTied;
                            @endphp
                            <div style="
                                display: grid;
                                grid-template-columns: 2.25rem 1fr 6rem;
                                gap: 0.5rem;
                                align-items: center;
                                padding: 0.5rem 0;
                                border-bottom: 1px solid rgba(0,0,0,0.06);
                                {{ $isTied ? 'background: rgba(251,191,36,0.08);' : '' }}
                            ">
                                {{-- Position badge --}}
                                <span style="
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 1.75rem;
                                    height: 1.75rem;
                                    border-radius: 50%;
                                    font-size: 0.75rem;
                                    font-weight: 700;
                                    {{ $isWinner
                                        ? 'background:#fef3c7; color:#b45309;'
                                        : 'background:rgba(0,0,0,0.06); color:#6b7280;' }}
                                ">
                                    {{ $isWinner ? '🏆' : ($row['position'] !== '' ? $row['position'].'.' : '—') }}
                                </span>

                                {{-- Team name --}}
                                <span style="font-size:0.875rem; font-weight:500; color:#111827; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $row['team_name'] }}
                                    @if($isTied || $wasShootout)<span style="margin-left:4px;">🎯</span>@endif
                                </span>

                                {{-- Score input --}}
                                <input
                                    type="number"
                                    min="0"
                                    wire:model.blur="rows.{{ $i }}.score"
                                    placeholder="—"
                                    style="
                                        width: 100%;
                                        border-radius: 0.5rem;
                                        border: 1px solid #d1d5db;
                                        background: #fff;
                                        padding: 0.375rem 0.75rem;
                                        font-size: 0.875rem;
                                        text-align: right;
                                        color: #111827;
                                        outline: none;
                                        box-sizing: border-box;
                                    "
                                />
                            </div>
                        @endforeach

                    </div>
                </div>

                <div style="margin-top:1rem; display:flex; justify-content:flex-end;">
                    <x-filament::button wire:click="save" icon="heroicon-o-check">
                        Uložit výsledky
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>
    @endif
</x-filament::widget>
