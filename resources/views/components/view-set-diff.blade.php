@props(['setDiff'])
<div x-data="viewSetDiff({{ json_encode($setDiff) }})">
    <!-- Common Elements -->
    <div>
        <h2 class="font-bold mb-4">Common Elements</h2>
        <span x-text="commonElements.join(', ')"></span>
    </div>

    <!-- Insertions -->
    <div class="mt-4">
        <h2 class="font-bold mb-4 text-green-600">Insertions</h2>
        <span x-text="insertions.join(', ')" class="bg-green-200"></span>
    </div>

    <!-- Deletions -->
    <div class="mt-4">
        <h2 class="font-bold mb-4 text-red-600">Deletions</h2>
        <span x-text="deletions.join(', ')" class="bg-red-200"></span>
    </div>
</div>

<script>
function viewSetDiff(setDiff) {
    console.log('setDiff:', setDiff); // Debugging line
    return {
        commonElements: setDiff.common || [],
        insertions: setDiff.insertions || [],
        deletions: setDiff.deletions || [],
    }
}
</script>
