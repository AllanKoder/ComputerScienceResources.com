<script setup>
defineProps({
    honeypot: Object,
    form: Object,
})
</script>


<template>
    <div v-if="honeypot.enabled" :name="`${honeypot.nameFieldName}_wrap`" style="display: none;">
        <input type="text" v-model="form[honeypot.nameFieldName]" :name="honeypot.nameFieldName" :id="honeypot.nameFieldName" />
        <input type="text" v-model="form[honeypot.validFromFieldName]" :name="honeypot.validFromFieldName" />
    </div>
</template>
