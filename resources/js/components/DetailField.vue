<template>
    <div class="flex border-b border-40">
        <div class="w-1/4 py-4">
            <h4 class="font-normal text-80">{{ field.name }}</h4>
        </div>

        <div class="w-3/4 py-4 break-words">
            <FilePond :field="field" :disabled="true" :allowMultiple="true" :allowRevert="false" label="Uploads are disabled, edit to upload." />
        </div>
    </div>
</template>

<script>
import FilePond from './FiledPond'

export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],
    components: {
        FilePond
    }
}
</script>
