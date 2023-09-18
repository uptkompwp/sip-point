<script lang="ts" setup>
import { getCurrentInstance, reactive, onMounted, ref } from 'vue'
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const data = reactive<{ pertemuan: string | null, tanggal: any | null, makul: any | null, kelas: any | null, tambahan: boolean }>({ pertemuan: null, tanggal: null, makul: null, kelas: null, tambahan: false })

const validations = ref<any>({})
const updateSesi = async (data) => {
    return await http.patch(`sesi/${route.params.id}`, { ...data, kelas: data.kelas ? data.kelas.value ?? null : null, makul: data.makul ? data.makul.value ?? null : null })
}
const getKelas = async (search) => {
    return await http.get(search.length ? `kelas/all?search=${search}` : `kelas/all`)
}
const getMakul = async (search) => {
    return await http.get(search.length ? `mata-kuliah/all?search=${search}` : `mata-kuliah/all`)
}

const toast = useToast()
const { isLoading, mutate } = useMutation(updateSesi, {
    onMutate() {
        validations.value = {}
    },
    onSuccess() {
        toast.success("berhasil update sesi", { position: "top" })
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
const route = useRoute()
const router = useRouter()

const getSesi = async () => {
    return await http.get(`/sesi/${route.params.id}/edit`);
}

const { mutate: get_edit, status } = useMutation(getSesi, {
    onSuccess(sesi) {
        data.pertemuan = sesi.data.pertemuan
        data.tanggal = sesi.data.tanggal
        data.tambahan = Boolean(sesi.data.tambahan)
        data.kelas = {
            label: sesi.data.kelas,
            value: sesi.data.id_kelas
        }
        data.makul = {
            label: sesi.data.makul,
            value: sesi.data.id_makul
        }
    },

    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('Sesi tidak di temukan', { position: 'top' })
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
    toast.clear();
    await get_edit()
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
        <div v-if="status === 'success'">
            <Buttonback />
            <div class="p-4 card border lg:max-w-lg w-full rounded-md">
                <h3 class="card-title mb-3">Edit Sesi Pertemuan</h3>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div class="form-control">
                        <input autofocus type="number"
                            :class="`input input-bordered w-full max-w-lg ${checkValidation(validations.pertemuan)}`"
                            placeholder="Pertemuan ke berapa ?" v-model="data.pertemuan" :disabled="isLoading">
                        <ValidationFeedback :validations="validations.pertemuan" />
                    </div>
                    <div class="form-control">
                        <VueDatePicker v-model="data.tanggal" cancel-text="Tutup" show-now-button
                            now-button-label="Sekarang" select-text="Pilih" :enable-time-picker="false"
                            model-type="yyyy-MM-dd" placeholder="Pilih tanggal pertemuan">
                        </VueDatePicker>
                        <ValidationFeedback :validations="validations.tanggal" />
                    </div>
                    <div class="form-control">
                        <SearchInput :model="data.kelas" @on-selected="setKelas" value="id" label="kelas"
                            :request="getKelas" label-input="Cari Kelas" no-opt="Ketik untuk mencari Kelas" />
                        <ValidationFeedback :validations="validations.kelas" />
                    </div>
                    <div class="form-control">
                        <SearchInput :model="data.makul" @on-selected="setMakul" value="id" label="makul"
                            :request="getMakul" label-input="Cari Mata kuliah" no-opt="Ketik untuk mencari Mata Kuliah" />
                        <ValidationFeedback :validations="validations.makul" />
                    </div>
                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <span class="label-text">
                                Checklist Jika Pertemuan Tambahan
                            </span>
                            <input type="checkbox" class="checkbox" v-model="data.tambahan" />
                        </label>
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
import SearchInput from '../../../components/SearchInput.vue';
import { useRoute, useRouter } from 'vue-router';
import Loading from '../../../components/Loading.vue';

export default {
    components: { Buttonback }
}
</script>
<style lang="">
    
</style>