<script lang="ts" setup>
import { getCurrentInstance, reactive, onMounted, ref } from 'vue'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ pertemuan: string | null, tanggal: any | null, makul: any | null, kelas: any | null, tambahan: boolean }>({ pertemuan: null, tanggal: null, makul: null, kelas: null, tambahan: false })

const validations = ref<any>({})
const storeSesi = async (data) => {
    return await http.post('sesi', { ...data, kelas: data.kelas ? data.kelas.value ?? null : null, makul: data.makul ? data.makul.value ?? null : null })
}
const getKelas = async (search) => {
    return await http.get(search.length ? `kelas/all?search=${search}` : `kelas/all`)
}
const getMakul = async (search) => {
    return await http.get(search.length ? `mata-kuliah/all?search=${search}` : `mata-kuliah/all`)
}

const toast = useToast()
const { isLoading, mutate } = useMutation(storeSesi, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil menambah pertemuan sesi", { position: "top" })
        data.pertemuan = null
        data.tanggal = null
        data.makul = null
        data.kelas = null
        data.tambahan = false
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

function setMakul(val) {
    data.makul = val
}
function setKelas(val) {
    data.kelas = val
}
</script>
<template>
    <div>
        <Buttonback />
        <div class="p-4 card border lg:max-w-lg w-full rounded-md">
            <h3 class="card-title mb-3">Tambahkan Sesi Pertemuan Baru</h3>
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div class="form-control">
                    <input autofocus type="number"
                        :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.pertemuan)}`"
                        placeholder="Pertemuan ke berapa ?" v-model="data.pertemuan" :disabled="isLoading">
                    <ValidationFeedback :validations="validations.pertemuan" />
                </div>
                <div class="form-control">
                    <VueDatePicker v-model="data.tanggal" cancel-text="Tutup" show-now-button now-button-label="Sekarang"
                        select-text="Pilih" :enable-time-picker="false" model-type="yyyy-MM-dd"
                        placeholder="Pilih tanggal pertemuan" :disabled="isLoading">
                    </VueDatePicker>
                    <ValidationFeedback :validations="validations.tanggal" />
                </div>
                <div class="form-control">
                    <SearchInput :model="data.kelas" @on-selected="setKelas" value="id" label="kelas" :disabled="isLoading" :request="getKelas"
                        label-input="Cari Kelas" no-opt="Ketik untuk mencari Kelas" />
                    <ValidationFeedback :validations="validations.kelas" />
                </div>
                <div class="form-control">
                    <SearchInput :model="data.makul" @on-selected="setMakul" value="id" label="makul" :disabled="isLoading" :request="getMakul"
                        label-input="Cari Mata kuliah" no-opt="Ketik untuk mencari Mata Kuliah" />
                    <ValidationFeedback :validations="validations.makul" />
                </div>
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">
                            Checklist Jika Pertemuan Tambahan
                        </span>
                        <input type="checkbox" class="checkbox" :disabled="isLoading" v-model="data.tambahan" />
                    </label>
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
import Buttonback from '../../../components/Buttonback.vue';
import { useToast } from 'vue-toast-notification';
import { checkValidation } from '../../../helpers/validation_helper';
import ValidationFeedback from '../../../components/ValidationFeedback.vue';
import SearchInput from '../../../components/SearchInput.vue';

export default {
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>