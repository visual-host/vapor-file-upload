Nova.booting((Vue, router, store) => {
  Vue.component('index-vapor-file-upload', require('./components/IndexField'))
  Vue.component('detail-vapor-file-upload', require('./components/DetailField'))
  Vue.component('form-vapor-file-upload', require('./components/FormField'))
})
