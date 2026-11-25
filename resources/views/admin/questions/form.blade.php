@extends('layouts.admin')
@section('title', $question->exists ? 'Edit question' : 'New question')
@section('heading', $question->exists ? 'Edit question' : 'New question · '.$exam->title)
@section('content')
<form method="POST" action="{{ $question->exists ? route('admin.questions.update', $question) : route('admin.exams.questions.store', $exam) }}"
      class="max-w-3xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($question->exists) @method('PUT') @endif

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Question *</label>
        <textarea name="question" rows="3" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('question', $question->question) }}</textarea>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Type</label>
            <select name="type" id="type" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                <option value="mcq" @selected(old('type', $question->type) === 'mcq')>MCQ</option>
                <option value="short" @selected(old('type', $question->type) === 'short')>Short answer</option>
                <option value="cq" @selected(old('type', $question->type) === 'cq')>Creative (CQ)</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Marks</label>
            <input type="number" step="0.25" name="marks" value="{{ old('marks', $question->marks ?? 1) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Position</label>
            <input type="number" name="position" value="{{ old('position', $question->position ?? 0) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        </div>
    </div>

    <div id="mcq-box" class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-sm font-semibold text-slate-700">Options <span class="font-normal text-slate-500">(select the correct one)</span></p>
        <div class="mt-3 space-y-2">
            @php($opts = old('options', $question->options->pluck('option_text')->all() ?: ['', '', '', '']))
            @php($correct = old('correct', $question->options->search(fn ($o) => $o->is_correct)))
            @foreach (array_pad($opts, 4, '') as $i => $opt)
                <div class="flex items-center gap-3">
                    <input type="radio" name="correct" value="{{ $i }}" @checked((int) $correct === $i) class="text-emerald-700">
                    <input name="options[{{ $i }}]" value="{{ $opt }}" placeholder="Option {{ chr(65 + $i) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700">Explanation (shown after the result)</label>
        <textarea name="explanation" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('explanation', $question->explanation) }}</textarea>
    </div>

    <div class="flex gap-2">
        <button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white">Save question</button>
        <a href="{{ route('admin.exams.questions.index', $exam) }}" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Back</a>
    </div>
</form>

<script>
    const type = document.getElementById('type');
    const box = document.getElementById('mcq-box');
    const toggle = () => box.style.display = type.value === 'mcq' ? 'block' : 'none';
    type.addEventListener('change', toggle); toggle();
</script>
@endsection
