@extends('layouts.student')
@section('title', 'Exam in progress')
@section('heading', $exam->title)
@section('content')

<div class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white px-5 py-3 shadow-sm">
    <span class="text-sm text-slate-600">Time remaining</span>
    <span id="timer" class="rounded-lg bg-slate-900 px-3 py-1 font-mono text-lg font-bold text-white">--:--</span>
    <span id="save-state" class="text-xs text-slate-500">Autosave ready</span>
    <div class="ml-auto flex items-center gap-2 text-sm">
        <span class="rounded-lg bg-emerald-50 px-3 py-1 font-semibold text-emerald-700">Answered: <span id="answered-count">0</span>/{{ $questions->count() }}</span>
        <button form="exam-form" type="submit" onclick="return confirm('Submit your answers now?')" class="rounded-lg bg-emerald-700 px-5 py-2 font-bold text-white hover:bg-emerald-800">Submit exam</button>
    </div>
</div>

<form id="exam-form" method="POST" action="{{ route('student.attempts.submit', $attempt) }}">
    @csrf
    <div class="grid gap-4 lg:grid-cols-[1fr_220px]">
        <div class="space-y-4">
            @foreach ($questions as $i => $question)
                @php($saved = $saved[$question->id] ?? null)
                <div id="q{{ $question->id }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-slate-900"><span class="mr-2 rounded bg-slate-200 px-2 py-0.5 text-xs font-bold text-slate-700">Q{{ $i + 1 }}</span>{{ $question->question }}</p>
                        <span class="shrink-0 rounded bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">{{ $question->marks }} mark{{ $question->marks > 1 ? 's' : '' }}</span>
                    </div>

                    @if ($question->type === 'mcq')
                        <div class="mt-4 space-y-2">
                            @foreach ($question->options as $option)
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 px-3 py-2 text-sm hover:bg-slate-50 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                           data-qid="{{ $question->id }}"
                                           @checked($saved && (int) $saved->option_id === $option->id)
                                           class="text-emerald-700 focus:ring-emerald-500">
                                    <span>{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <textarea name="answers[{{ $question->id }}]" data-qid="{{ $question->id }}" rows="4"
                                  placeholder="Write your answer…"
                                  class="mt-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $saved->answer_text ?? '' }}</textarea>
                        <p class="mt-1 text-xs text-amber-600">Descriptive answers are reviewed manually by your instructor.</p>
                    @endif
                </div>
            @endforeach
        </div>

        <aside class="h-fit space-y-3 lg:sticky lg:top-24">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Question navigator</p>
                <div class="mt-3 grid grid-cols-5 gap-2">
                    @foreach ($questions as $i => $question)
                        <button type="button" onclick="document.getElementById('q{{ $question->id }}').scrollIntoView({behavior:'smooth', block:'center'})"
                                data-nav="{{ $question->id }}"
                                class="nav-dot grid h-9 place-items-center rounded-lg border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">{{ $i + 1 }}</button>
                    @endforeach
                </div>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">
                Answers autosave every 15 seconds. The exam submits automatically when the timer hits zero.
            </div>
        </aside>
    </div>
</form>

<script>
(function () {
    const form = document.getElementById('exam-form');
    const total = {{ (int) $remaining }};
    const submitUrl = @json(route('student.attempts.save', $attempt));
    const submitFinal = @json(route('student.attempts.submit', $attempt));
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let left = total;

    const render = () => {
        const m = String(Math.floor(left / 60)).padStart(2, '0');
        const s = String(left % 60).padStart(2, '0');
        document.getElementById('timer').textContent = `${m}:${s}`;
        document.getElementById('timer').classList.toggle('bg-rose-600', left < 60);
    };
    render();

    const tick = setInterval(() => {
        left--;
        render();
        if (left <= 0) {
            clearInterval(tick);
            document.getElementById('timer').textContent = '00:00';
            form.submit();
        }
    }, 1000);

    const refreshAnswered = () => {
        const qids = new Set();
        form.querySelectorAll('input[type=radio]:checked').forEach(el => qids.add(el.dataset.qid));
        form.querySelectorAll('textarea').forEach(el => { if (el.value.trim()) qids.add(el.dataset.qid); });
        document.getElementById('answered-count').textContent = qids.size;
        form.querySelectorAll('.nav-dot').forEach(dot => {
            const on = qids.has(dot.dataset.nav);
            dot.classList.toggle('bg-emerald-700', on);
            dot.classList.toggle('text-white', on);
            dot.classList.toggle('border-emerald-600', on);
        });
    };
    refreshAnswered();
    form.addEventListener('input', refreshAnswered);
    form.addEventListener('change', refreshAnswered);

    const save = (final) => {
        const data = new FormData(form);
        data.append('_token', csrf);
        if (final) data.append('submit', '1');
        return fetch(submitUrl, { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json());
    };

    setInterval(() => {
        save(false).then(res => {
            if (res && res.saved_at) {
                document.getElementById('save-state').textContent = 'Saved ' + res.saved_at;
            }
        }).catch(() => {});
    }, 15000);
})();
</script>
@endsection
