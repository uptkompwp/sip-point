<script lang="ts" setup>
import { FolderIcon, PencilIcon, PlusIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useMutation } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { getCurrentInstance, reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';
import Datatable from '../../../components/Datatable.vue';
import Buttonback from '../../../components/Buttonback.vue';
import Loading from '../../../components/Loading.vue';
const columns = reactive([
    "Nama Kuis",
    "Tipe",
    "Mahasiswa yang mendapatkan point",
    "Actions",
])
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const requestDelete = async (id) => {
    return await http.delete(`/kuis/${id}`)
}

const router = useRouter()
const route = useRoute()
const update = ref(false)
const { mutate } = useMutation(requestDelete, {
    onSuccess() {
        toast.success("berhasil menghapus kuis", { position: "top-right" })
        router.resolve({ name: route.name! });
        update.value = true
    },
    onError() {
        toast.error("Terjadi kesalahan", { position: "top-right" })
    },
    onSettled() {
        update.value = false
    },
})
const toast = useToast()



const handleDelete = (id) => {
    Swal.fire({
        title: 'Yakin ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Hapus',
        cancelButtonText: "Batalkan"
    }).then(async (result) => {
        if (result.isConfirmed) {
            await mutate(id)
        }
    })
}


const reset_req = async () => {
    return await http.post(`kuis/${route.params.sesi_id}/reset`)
}

const { mutate: reset, isLoading: loading_reset } = useMutation(reset_req, {
    onSuccess() {
        toast.success('Berhasil mereset semua data kelas', { position: "top-right" })
        update.value = true
    },
    onError() {
        toast.error('Terjadi kesalahan', { position: "top-right" })
    }, onSettled() {
        update.value = false
    },
})
const handleReset = () => {
    Swal.fire({
        title: 'Yakin ingin mereset semua data ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Reset',
        cancelButtonText: "Batalkan"
    }).then(async (result) => {
        if (result.isConfirmed) {
            await reset()
        }
    })
}


const getSesi = async () => {
    return await http.get(`/sesi/${route.params.sesi_id}/edit`);
}

const { mutate: get_edit, status, data } = useMutation(getSesi, {
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


const batch_remove = async (checkeds) => {
    return await http.post('kuis/delete-selected', { checkeds })
}

const batch_actions = ref<string>("")
const { mutate: batch_remove_handle, status: batch_remove_status } = useMutation(batch_remove, {
    onSuccess() {
        toast.success('Berhasil menghapus data mahasiswa', { position: "top-right" })
        update.value = true
    }, onError() {
        toast.error('Terjadi kesalahan', { position: "top-right" })
    }, onSettled() {
        update.value = false
        batch_actions.value = ""
    },
})
const handleBatch = async (checkeds) => {
    if (batch_actions.value.length) {
        switch (batch_actions.value) {
            case "Hapus":
                await batch_remove_handle(checkeds)
                break;
        }
    }
}

</script>
<template>
    <div class="p-3">
        <div v-if="status === 'success'">
            <Buttonback />
            <h3 class="text-lg mb-4 font-semibold">
                Management Kuis
            </h3>
            <div class="divider max-w-lg">
                Detail Sesi Pertemuan
            </div>
            <ul class="menu bg-base-200 max-w-lg space-y-2 font-semibold rounded-box">
                <li>
                    Pertemuan : {{ data.data.pertemuan }}
                </li>
                <li>
                    Kelas : {{ data.data.kelas }}
                </li>
                <li>
                    Mata Kuliah : {{ data.data.makul }}
                </li>
                <li>
                    Tanggal : {{ data.data.tanggal }}
                </li>
                <li>
                    Kelas Tambahan : {{ Boolean(data.data.tambahan) ? "YA" : "Tidak" }}
                </li>
            </ul>
            <div class="flex lg:flex-row items-start gap-3 flex-wrap mt-3">
                <router-link class="btn btn-neutral btn-sm"
                    :to="{ name: 'create-kuis', params: { sesi_id: route.params.sesi_id } }">
                    <PlusIcon class="h-5 w-5" /> Add new
                </router-link>
                <button :disabled="loading_reset" @click.prevent="handleReset" class="btn btn-error btn-outline btn-sm">
                    <XMarkIcon class="h-5 w-5" /> Reset Data
                </button>
            </div>
            <Datatable :url="`kuis/${route.params.sesi_id}/sesi`" :columns="columns" :update="update" checked="id">
                <template #batchActions="data">
                    <form class="flex max-w-sm w-full space-x-4" @submit.prevent="handleBatch(data.checkeds)">
                        <select class="select w-full select-bordered" v-model="batch_actions"
                            :disabled="batch_remove_status === 'loading'">
                            <option value="">--Pilih Actions ({{ data.checkeds.length }})--</option>
                            <option value="Hapus">Hapus ({{ data.checkeds.length }})</option>
                        </select>
                        <button class="btn btn-neutral" type="submit"
                            :disabled="!data.checkeds.length || batch_remove_status === 'loading'">Submit</button>
                    </form>
                </template>
                <template #content="data">
                    <td>
                        {{ data.data.kuis }}
                    </td>

                    <td>
                        <span class="badge badge-neutral">
                            {{ data.data.tipe_kuis }}
                        </span>
                    </td>
                    <td>
                        {{ data.data.points_count }}
                    </td>
                    <th class="flex flex-wrap gap-2">
                        <router-link :to="{ name: 'edit-kuis', params: { id: data.data.id } }"
                            class="btn btn-sm btn-neutral btn-outline">
                            <PencilIcon class="w-4 h-4" />
                        </router-link>
                        <button @click.prevent="handleDelete(data.data.id)" class="btn btn-sm btn-neutral btn-outline">
                            <TrashIcon class="w-4 h-4" />
                        </button>
                        <router-link :to="{ name: 'detail-kuis', params: { id: data.data.id } }"
                            class="btn btn-sm btn-neutral btn-outline tooltip" data-tip="Lihat detail Kuis">
                            <FolderIcon class="w-4 h-4 mt-2" />
                        </router-link>
                    </th>
                </template>
            </DataTable>
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