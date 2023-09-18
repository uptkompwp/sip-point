<script lang="ts" setup>
import { getCurrentInstance, onMounted } from 'vue'
import { useMutation } from '@tanstack/vue-query'
import Loading from '../../../components/Loading.vue'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const importAction = async (data) => {
    return await http.post('mahasiswa/import', data)
}
const saveAction = async (data) => {
    return await http.post('mahasiswa/import-save', { data: data })
}
const toast = useToast()
const { mutate, status, data, reset } = useMutation(importAction, { onError() { toast.error("terjadi kesalahan", { position: 'top-right' }) } })
// simpan mutation
const { mutate: saveData, status: status_save, data: data_save, reset: reset_save } = useMutation(saveAction, {
    onSuccess() {
        toast.success("Berhasil import data dari excel", { position: 'top-right' })
        reset()
    },
    onError() {
        toast.error("terjadi kesalahan", { position: 'top-right' })
    }
})

const handleChange = async (e) => {
    reset_save()
    const file = e.target.files[0];
    const formdata = new FormData();
    formdata.append('file', file)
    await mutate(formdata)
}

const handleSave = async () => {
    await saveData(data.value.data.data)
}


onMounted(() => {
    toast.clear()
    reset_save()
    reset()
})
</script>
<template>
    <div class="p-4">
        <Buttonback />
        <div class="overflow-x-auto">
            <div class="mb-5">
                <input type="file" class="file-input w-full max-w-xs file-input-sm" @change="handleChange"
                    v-if="status != 'success'" :disabled="status === 'loading'" />
                <div v-if="status === 'success'" class="flex space-x-3">
                    <button @click="reset" class="btn btn-error btn-sm" :disabled="status_save === 'loading'">Clear</button>
                    <button @click.prevent="handleSave" class="btn btn-success btn-sm"
                        :disabled="status_save === 'loading'">{{ status_save === 'loading' ? "Import..." : `Import
                        ${data.data.data.length}`
                        }}</button>
                </div>
            </div>
            <div class="mockup-code" v-if="status_save === 'success'">
                <pre data-prefix=">" class="text-success"><code>{{ data_save.data.success }} Berhasil</code></pre>
                <pre data-prefix="X" class="text-error"><code>{{ data_save.data.failed }} Gagal</code></pre>
            </div>
            <table class="table" v-if="status === 'success'">
                <thead>
                    <tr class="bg-neutral text-white">
                        <th>#</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-base-200" v-for="(excel, index) in data.data.data" :key="index">
                        <th>{{ index + 1 }}</th>
                        <td>{{ excel.nim }}</td>
                        <td>{{ excel.nama }}</td>
                        <td>{{ excel.kelas }}</td>
                    </tr>
                </tbody>
            </table>
            <Loading v-if="status === 'loading'" />
        </div>
    </div>
</template>
<script lang="ts">
import Buttonback from '../../../components/Buttonback.vue';
import { useToast } from 'vue-toast-notification';

export default {
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>