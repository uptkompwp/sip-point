<script lang="ts" setup>
import { getCurrentInstance, onMounted, reactive, ref } from 'vue';
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ nim: string | null, nama: string | null, kelas: string | null }>({ nim: null, nama: null, kelas: null })

const validations = ref<any>({})
const storeMahasiswa = async (data) => {
    return await http.post('mahasiswa', { ...data, kelas: data.kelas ? data.kelas.value ?? null : null })
}
const getKelas = async (search) => {
    return await http.get(search.length ? `kelas/all?search=${search}` : `kelas/all`)
}

const toast = useToast()
const { isLoading, mutate } = useMutation(storeMahasiswa, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil menambah mahasiswa", { position: "top" })
        data.nim = null
        data.nama = null
        data.kelas = null
    },
    onError(error: any) {
        switch (error.response.data.context) {
            case "VALIDATIONS":
                validations.value = error.response.data.validations
                break;
            case "INTERNAL_SERVER_ERROR":
                toast.error('Internal server error', { position: 'top' })
                break;
            default:
                toast.error('Internal server error', { position: 'top' })
                break;
        }
    },
})

const handleSubmit = async () => {
    await mutate(data)

}

onMounted(() => {
    toast.clear();
})

function setKelas(val) {
    data.kelas = val
}
</script>
<template>
    <div>
        <Buttonback />
        <div class="p-4 card border lg:max-w-lg w-full rounded-md">
            <h3 class="card-title mb-3">Tambahkan Mahasiwa baru</h3>
            <form @submit.prevent="handleSubmit" class="space-y-4">

                <input autofocus type="text"
                    :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.nim)}`"
                    placeholder="Masukan NIM mahasiwa" v-model="data.nim" :disabled="isLoading">
                <ValidationFeedback :validations="validations.nim" />

                <input type="text" :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.nama)}`"
                    placeholder="Masukan nama mahasiswa" v-model="data.nama" :disabled="isLoading">
                <ValidationFeedback :validations="validations.nama" />
                <div>
                    <SearchInput :model="data.kelas" @on-selected="setKelas" value="id" label="kelas" :request="getKelas"
                        label-input="Cari Kelas" no-opt="Ketik untuk mencari kelas" :disabled="isLoading" />
                    <ValidationFeedback :validations="validations.kelas" />
                </div>
                <button class="btn btn-neutral" type="submit" :disabled="isLoading">
                    <span class="loading loading-spinner" v-if="isLoading"></span>
                    Simpan
                </button>
            </form>
        </div>
    </div>
</template>
<script lang="ts">
import { useMutation } from '@tanstack/vue-query';
import { useToast } from 'vue-toast-notification';
import Buttonback from '../../../components/Buttonback.vue';
import SearchInput from '../../../components/SearchInput.vue';
import ValidationFeedback from '../../../components/ValidationFeedback.vue';
import { checkValidation } from '../../../helpers/validation_helper';

export default {
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>