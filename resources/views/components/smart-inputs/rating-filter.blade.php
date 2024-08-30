@props(['name', 'useQueryParameters'=>false, 'initialVal'=>2, 'defaultVal'=>1])

<div x-data="ratingFilter('{{$name}}', {{$initialVal}}, {{$defaultVal}}, {{ $useQueryParameters ? 'true' : 'false'}})"
@clear-inputs-event.window="resetInputs()"
x-init='initialize()'
class="flex items-center gap-1">
    <label for="oneStar-{{$name}}" class="cursor-pointer transition hover:scale-125 has-[:focus]:scale-125">
        <span class="sr-only">one star</span>
        <input x-model="currentVal" id="oneStar-{{$name}}" type="radio" class="sr-only" name="{{$name}}" value="1">
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" :class="currentVal > 0 ? 'text-amber-500' : 'text-neutral-600 dark:text-neutral-300'">
            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd">
        </svg>
    </label>

    <label for="twoStar-{{$name}}" class="cursor-pointer transition hover:scale-125 has-[:focus]:scale-125">
        <span class="sr-only">two stars</span>
        <input x-model="currentVal" id="twoStar-{{$name}}" type="radio" class="sr-only" name="{{$name}}" value="2">
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" :class="currentVal > 1 ? 'text-amber-500' : 'text-neutral-600 dark:text-neutral-300'">
            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd">
        </svg>
    </label>

    <label for="threeStar-{{$name}}" class="cursor-pointer transition hover:scale-125 has-[:focus]:scale-125">
        <span class="sr-only">three stars</span>
        <input x-model="currentVal" id="threeStar-{{$name}}" type="radio" class="sr-only" name="{{$name}}" value="3">
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" :class="currentVal > 2 ? 'text-amber-500' : 'text-neutral-600 dark:text-neutral-300'">
            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd">
        </svg>
    </label>

    <label for="fourStar-{{$name}}" class="cursor-pointer transition hover:scale-125 has-[:focus]:scale-125">
        <span class="sr-only">four stars</span>
        <input x-model="currentVal" id="fourStar-{{$name}}" type="radio" class="sr-only" name="{{$name}}" value="4">
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" :class="currentVal > 3 ? 'text-amber-500' : 'text-neutral-600 dark:text-neutral-300'">
            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd">
        </svg>
    </label>

    <span class="font-semibold"> & Up </span>
</div>  

<script>
    function ratingFilter(name, initialVal, defaultVal, useQueryParameters) {
        return {
            currentVal: initialVal,
            initialize() {
                if (useQueryParameters == true) 
                {
                    this.currentVal = Alpine.store('getQueryParameter')(name) ?? 1;
                    if (this.currentVal < 1) this.currentVal = 1;
                    else if (this.currentVal > 5) this.currentVal = 5;
                }
                else
                {
                    this.currentVal = initialVal;
                }
            },
            resetInputs() {
                this.currentVal = defaultVal;
            },
        }
    }
</script>
