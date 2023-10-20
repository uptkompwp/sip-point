<script lang="ts" setup>
import { ArrowDownIcon, CloudIcon, PlusIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { useMutation } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { getCurrentInstance, reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toast-notification';
import Datatable from '../../../components/Datatable.vue';
import Buttonback from '../../../components/Buttonback.vue';
import Loading from '../../../components/Loading.vue';
import SearchInput from '../../../components/SearchInput.vue';
import { downloadHelper } from '../../../helpers/download_helper';
const columns = reactive([
    "Sesi Pertemuan",
    "Point",
    "Mahasiswa",
    "Actions",
])
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: any }

const requestDelete = async (id) => {
    return await http.delete(`/point/${id}`)
}

const router = useRouter()
const route = useRoute()
const update = ref(false)
const { mutate } = useMutation(requestDelete, {
    onSuccess() {
        toast.success("berhasil menghapus point", { position: "top-right" })
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
    return await http.post(`point/${route.params.id}/reset`)
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
    return await http.get(`/kuis/${route.params.id}/edit`);
}

const { mutate: get_edit, status, data } = useMutation(getSesi, {
    onError(error: any) {
        switch (error.response.data.context) {
            case "NOT_FOUND":
                toast.warning('Kuis tidak di temukan', { position: 'top' })
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
const filters = reactive<{ mahasiswa?: any }>({})


const getMhs = async (search) => {
    return await http.get(search.length ? `mahasiswa/all?search=${search}` : `mata-kuliah/all`)
}

function setMhs(val) {
    filters.mahasiswa = val
}

const download_req = async () => {
    return await http.get('point-download-format', { responseType: "blob" })
}

const { mutate: download, status: download_status } = useMutation(download_req, {
    onSuccess(data) {
        downloadHelper(data.data, 'point.xlsx')
    }
})
const update_req = async (data) => {
    return await http.patch(`point/${data.id}`, { point: data.point })
}

const { mutate: update_point, status: update_status } = useMutation(update_req, {
    onSuccess() {
        toast.success("Berhasil update point", { position: 'top-right' })
    },
    onError() {
        toast.error("Terjadi kesalahan", { position: 'top-right' })
    }
})

const handleDownload = async () => {
    await download()
}
const handleBlur = async (e, id, point) => {
    const value = parseInt(e.target.innerText);
    if (value != point) {
        await update_point({ id, point: value })
    }
}

</script>
<template>
    <div class="p-3">
        <div v-if="status === 'success'">
            <Buttonback />
            <h3 class="text-lg mb-4 font-semibold">
                Management Point
            </h3>
            <div class="divider max-w-lg">
                Detail Kuis
            </div>
            <ul class="menu bg-base-200 max-w-lg space-y-2 font-semibold rounded-box">
                <li>
                    Kuis : {{ data.data.kuis }}
                </li>
                <li>
                    Tipe Kuis : {{ data.data.tipe_kuis }}
                </li>
            </ul>
            <div class="flex lg:flex-row items-start gap-3 flex-wrap mt-3">
                <router-link class="btn btn-neutral btn-sm"
                    :to="{ name: 'create-point', params: { id: route.params.id, sesi_id: data.data.id_sesi } }">
                    <PlusIcon class="h-5 w-5" /> Add Point
                </router-link>
                <button :disabled="loading_reset" @click.prevent="handleReset" class="btn btn-error btn-outline btn-sm">
                    <XMarkIcon class="h-5 w-5" /> Reset Data
                </button>
                <button :disabled="download_status === 'loading'" @click.prevent="handleDownload"
                    class="btn btn-success btn-sm">
                    <ArrowDownIcon class="h-5 w-5" />
                    {{ download_status === 'loading' ? "Downloading...." : "Download Format (Excel)" }}
                </button>
                <router-link :to="{ name: 'import-point' }" class="btn btn-success btn-sm">
                    <CloudIcon class="h-5 w-5" /> Import (Excel)
                </router-link>
            </div>
            <div class="w-full max-w-sm mt-3">
                <SearchInput :model="filters.mahasiswa" @on-selected="setMhs" value="id" label="nim" :request="getMhs"
                    label-input="Cari NIM mahasiswa" no-opt="Ketik untuk mencari NIM mahasiswa" />
            </div>
            <Datatable :url="`point/${route.params.id}`" :columns="columns" :update="update" :showSearch="false"
                :filter="{ ...filters.mahasiswa != null && { mahasiswa: (filters.mahasiswa as any).label } }">
                <template #content="data">
                    <td>
                        <div class="overflow-x-auto">
                            <table class="table table-xs">
                                <tr>
                                    <th>Pertemuan</th>
                                    <td>{{ data.data.kuis.sesi.pertemuan }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>{{ data.data.kuis.sesi.tanggal }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td :contenteditable="update_status == 'loading' ? false : true"
                        @blur="handleBlur($event, data.data.id, data.data.point)">
                        {{ data.data.point }}
                    </td>
                    <td>
                        <div class="overflow-x-auto">
                            <table class="table table-xs">
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ data.data.mahasiswa.nama }}</td>
                                </tr>
                                <tr>
                                    <th>NIM</th>
                                    <td>{{ data.data.mahasiswa.nim }}</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>{{ data.data.mahasiswa.kelas.kelas }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <th class="flex flex-wrap gap-2">
                        <button @click.prevent="handleDelete(data.data.id)" class="btn btn-sm btn-neutral btn-outline">
                            <TrashIcon class="w-4 h-4" />
                        </button>
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
