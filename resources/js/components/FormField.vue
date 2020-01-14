<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <file-pond :field="field" @setPond="setPond" :disabled="false" :allowMultiple="field.allowMultiple" :maxFiles="field.maxFiles" :allowRevert="true" />
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import { map } from 'lodash'
import FilePond from './FiledPond'

export default {
    mixins: [
        FormField,
        HandlesValidationErrors
    ],
    props: [
        'resourceName',
        'resourceId',
        'field'
    ],
    methods: {
        setPond(pond) {
            this.pond = pond
        },
        fill(formData) {
            formData.append(this.field.attribute, this.getPondFiles() || '')
        },
        getPondFiles() {
            return map(this.pond.getFiles(), file => {
                return file.serverId
            })
        }
    },
    components: {
        FilePond
    }
}
</script>
