<template>
    <div class="filepond">
        <input type="file" ref="pond" data-pond>
    </div>
</template>

<script>
let bucketEndpoint = null;

import { map, startsWith, split, filter, size } from 'lodash'
import Vapor from 'laravel-vapor'
import axios from 'axios'

// Filepond
import * as FilePond from 'filepond'

import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'

import 'filepond/dist/filepond.min.css'

FilePond.registerPlugin(FilePondPluginFileValidateType)
FilePond.registerPlugin(FilePondPluginImagePreview)

FilePond.setOptions({
    server: {
        process: async (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
            let upload = await Vapor.store(file, {
                visibility: 'public-read',
                progress: vaporProgress => {
                    let uploadProgress = Math.round(vaporProgress * 100)
                    progress(true, uploadProgress, 100)
                }
            })

            if (upload.key) {
                load(`${upload.key}.${upload.extension}`)
            } else {
                error('There was an error uploading your file, please try again.')
            }

            return {
                abort: () => {
                    abort()
                }
            }
        },
        load: async (source, load, error, progress, abort, headers) => {
            let head = await axios.head(`${bucketEndpoint}/${source}`)

            if (startsWith(head.headers['content-type'], 'image')) {
                let file = await axios.get(`${bucketEndpoint}/${source}`, {
                    responseType: 'blob',
                    timeout: 30000,
                })

                load(file.data)
            } else {
                load(head.data)
            }
        }
    }
})

FilePond.parse(document.body)

export default {
    data() {
        return {
            pond: null,
            bucketEndpoint: null
        }
    },
    props: [
        'field',
        'label',
        'disabled',
        'allowMultiple',
        'allowRevert',
        'maxFiles'
    ],
    async mounted() {
        let { data: endpoint } = await axios.get('/api/filepond/url');
        this.bucketEndpoint = endpoint.url;

        // TODO: Find better solution
        bucketEndpoint = endpoint.url;

        let pondFiles = map(this.field.value, file => {
            if (file) {
                return {
                    source: file,
                    options:{
                        type: 'local',
                        metadata: {
                            url: `${this.bucketEndpoint}/${file}`
                        }
                    }
                }
            }
        })

        // Remove blank arrays
        pondFiles = filter(pondFiles, size)

        this.pond = FilePond.create(this.$refs.pond, {
            files: pondFiles,
            maxFiles: this.maxFiles || 50,
            allowMultiple: !!this.allowMultiple,
            allowRevert: !!this.allowRevert,
            disabled: !!this.disabled,
            labelIdle: this.label || 'Drag & drop your files or <span class="filepond--label-action">click here to browse</span>',
            allowPaste: false,
            onactivatefile: fileObject => {
                let url = fileObject.getMetadata('url')

                if (typeof url != 'undefined') {
                    let a = document.createElement('a')
                    document.body.appendChild(a)
                    a.setAttribute('target', '_blank')
                    a.style.display = 'none'
                    a.href = url
                    a.click()
                    a.remove()
                }
            }
        })

        this.$emit('setPond', this.pond)
    },
}
</script>

<style lang="scss">
    .filepond {
        .filepond--root {
            .filepond--file {
                cursor: pointer;
            }
        }
    }
</style>