@props(['textDiff'])
<div x-data="viewTextDiff({{ json_encode($textDiff) }})">
    <template x-for="diff in textDiffArray" :key="diff.text">
        <span :class="{
            'bg-green-200': diff.type === 'insertion',
            'bg-red-200': diff.type === 'deletion'
        }">
            <template x-if="diff.type === 'insertion'">
                <span>+<span x-text="diff.text"></span></span>
            </template>
            <template x-if="diff.type === 'deletion'">
                <span>-<span x-text="diff.text"></span></span>
            </template>
            <template x-if="diff.type === 'normal'">
                <span x-text="diff.text"></span>
            </template>
        </span>
    </template>
</div>

<script>
function viewTextDiff(textDiff) {
    return {
        textDiffArray: textDiff,
    }
}
</script>
