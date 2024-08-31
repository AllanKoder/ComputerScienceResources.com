<script>
    let counter = 0;

    function increment() {
        counter += 1;
    }
</script>


<p>testing</p>
<button on:click={increment}>
    <span>{counter}</span>
</button>