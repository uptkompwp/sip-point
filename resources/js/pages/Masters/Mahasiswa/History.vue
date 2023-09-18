<script lang="ts" setup>
import { reactive, onMounted, getCurrentInstance } from 'vue';
import Datatable from '../../../components/Datatable.vue';
import { useRoute, useRouter } from 'vue-router';
import Buttonback from '../../../components/Buttonback.vue';
import { useToast } from 'vue-toast-notification';
import { useMutation } from '@tanstack/vue-query';
import Loading from '../../../components/Loading.vue';
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const columns = reactive([
    "Pertemuan",
    "Tanggal Sesi",
    "Point",
    "Nama Sesi",
    "Tipe Kuis",
])
const route = useRoute()
const router = useRouter()
const toast = useToast()
const getMahasiswa = async () => {
    return await http.get(`/mahasiswa/${route.params.id}/edit`);
}
const { mutate: get_edit, status } = useMutation(getMahasiswa, {

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

</script>
<template>
    <div>
        <div v-if="status === 'success'">
            <div class="p-3">
                <Buttonback />
                <h3 class="text-lg mb-4 font-semibold">
                    History Mahasiswa
                </h3>
                <Datatable :url="`mahasiswa/${route.params.id}/history`" :columns="columns">
                    <template #content="data">
                        <td>
                            {{ data.data.pertemuan }}
                        </td>
                        <td>
                            {{ data.data.tanggal }}
                        </td>
                        <td>
                            {{ data.data.point }}
                        </td>
                        <td>
                            {{ data.data.kuis }}
                        </td>
                        <td>
                            {{ data.data.tipe_kuis }}
                        </td>
                    </template>
                </DataTable>
            </div>
        </div>
        <Loading v-if="status === 'loading'" />

    </div>
</template>
<script lang="ts">
export default {

}
</script>
<style lang="">
    
</style>