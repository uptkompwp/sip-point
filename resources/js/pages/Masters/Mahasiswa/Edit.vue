<script lang="ts" setup>
import { getCurrentInstance, reactive, onMounted, ref } from 'vue'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ nim: string | null, nama: string | null, kelas: any | null }>({ nim: null, nama: null, kelas: null })
const validations = ref<any>({})
const route = useRoute()
const router = useRouter()
const updateMakul = async (data) => {
    return await http.patch(`mahasiswa/${route.params.id}`, { ...data, kelas: data.kelas ? data.kelas.value ?? null : null })
}

const toast = useToast()
const { isLoading, mutate } = useMutation(updateMakul, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil update mahasiswa", { position: "top" })
    },
    onError(error: any) {
        switch (error.response.data.context) {
            case "VALIDATIONS":
                validations.value = error.response.data.validations
                break;
            case "NOT_FOUND":
                router.push({ name: 'mahasiswa' })
            default:
                toast.error('Internal server error', { position: 'top' })
                break;
        }
    },
})

const handleSubmit = async () => {
    await mutate(data)
}

const getMahasiswa = async () => {
    return await http.get(`/mahasiswa/${route.params.id}/edit`);
}
const getKelas = async (search) => {
    return await http.get(search.length ? `kelas/all?search=${search}` : `kelas/all`)
}

const { mutate: get_edit, status } = useMutation(getMahasiswa, {
    onSuccess(mahasiswa) {
        data.nim = mahasiswa.data.nim
        data.nama = mahasiswa.data.nama
        data.kelas = {
            label: mahasiswa.data.kelas,
            value: mahasiswa.data.id_kelas
        }
    },

    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('Mahasiswa tidak di temukan', { position: 'top' })
                router.back()
                break;
            default:
                toast.error('Internal server error', { position: 'top' })
                router.back()
                break;
        }
    },
})


onMounted(async () => {
    await get_edit()
    toast.clear();
})
function setKelas(val) {
    data.kelas = val
}
</script>
<template>
    <div>
        <div v-if="status === 'success'">
            <Buttonback />
            <div class="p-4 card border lg:max-w-lg w-full rounded-md">
                <h3 class="card-title mb-3">Edit Mahasiswa</h3>
                <form @submit.prevent="handleSubmit" class="space-y-4">

                    <input autofocus type="text"
                        :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.nim)}`"
                        class="input input-bordered w-full max-w-lg" placeholder="Masukan NIM mahasiwa" v-model="data.nim"
                        :disabled="isLoading">
                    <ValidationFeedback :validations="validations.nim" />

                    <input type="text" :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.sks)}`"
                        class="input input-bordered w-full max-w-lg" placeholder="Masukan nama mahasiswa"
                        v-model="data.nama" :disabled="isLoading">
                    <ValidationFeedback :validations="validations.sks" />
                    <div>
                        <SearchInput :model="data.kelas" @on-selected="setKelas" value="id" label="kelas"
                            :request="getKelas" label-input="Cari Kelas" no-opt="Ketik untuk mencari kelas" />
                        <ValidationFeedback :validations="validations.kelas" />
                    </div>
                    <button class="btn btn-neutral" type="submit" :disabled="isLoading">
                        <span class="loading loading-spinner" v-if="isLoading"></span>
                        Update
                    </button>
                </form>
            </div>
        </div>
        <Loading v-if="status === 'loading'" />
    </div>
</template>
<script lang="ts">
import { useMutation } from '@tanstack/vue-query';
import Buttonback from '../../../components/Buttonback.vue';
import { useToast } from 'vue-toast-notification';
import { checkValidation } from '../../../helpers/validation_helper';
import ValidationFeedback from '../../../components/ValidationFeedback.vue';
import { useRoute, useRouter } from 'vue-router';
import Loading from '../../../components/Loading.vue';
import SearchInput from '../../../components/SearchInput.vue';

export default {
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>