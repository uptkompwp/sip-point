<script lang="ts" setup>
import { useQuery } from '@tanstack/vue-query';
import { Axios } from 'axios';
import { Ref, getCurrentInstance, reactive, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { debounce } from '../helpers/debounce';
const route = useRoute()
const router = useRouter();
const { axios: http } = getCurrentInstance()?.appContext.app as unknown as { axios: Axios }

const props = defineProps({
    url: String,
    checked: String,
    columns: Array,
    update: Boolean,
    filter: Object,
    showSearch: {
        type: Boolean,
        default: true
    }
})

const query = route.query as { search?: string, show?: number, page?: number, filter?: any }
const current_page = ref<number>(query.page ?? 1);
const getData = async (queryParams: { search?: string, show?: number, page?: number, filter?: any }, page: Ref<number>) => {
    const params = new URLSearchParams({
        ...queryParams.show && { show: queryParams.show.toString() },
        ...queryParams.search && { search: queryParams.search },
        page: page.value.toString(),
        ...queryParams.filter && Object.keys(queryParams.filter).length && { filter: JSON.stringify(queryParams.filter) }
    })

    return await http.get(`/${props.url}?${params.toString()}`)
}


const queryParams = reactive<{ search?: string, show?: number, filter?: any }>({
    show: query.show ? [5, 10, 20, 50].includes(parseInt(query.show as any)) ? query.show : 10 : 10,
    search: query.search,
})


const { data, status, refetch } = useQuery({
    queryKey: [props.url, queryParams, current_page],
    queryFn: () => getData(queryParams, current_page),
    keepPreviousData: false,
},)
watch([queryParams, current_page, () => props.filter], function () {
    //   filter
    if (props.filter && Object.keys(props.filter as any).length) {
        queryParams.filter = JSON.stringify(props.filter);
    } else {
        delete queryParams.filter;
    }
    debounce(
        router.replace({ name: route.name!, query: { ...queryParams, page: current_page.value } }), 500
    )
}, { deep: true })



const handleClickPagination = (page: number | null) => {
    if (page != null) {
        current_page.value = page
    }
}

const checkeds = ref<Array<any>>([])

const handleChange = (e) => {
    const index = checkeds.value.indexOf(e.target.value);
    if (e.target.checked) {
        if (index != -1) {
            checkeds.value.splice(index, 1)
        } else {
            checkeds.value.push(e.target.value)
        }
    } else {
        if (index != -1) {
            checkeds.value.splice(index, 1)
        }
    }
}

watch(() => props.update, (val) => {
    if (val) {
        refetch()
        checkeds.value = []
    }
})


</script>
<template>
    <div class="grid gap-y-3 py-4">
        <div class="flex items-center lg:flex-row flex-col gap-y-4 lg:gap-y-0 mb-5 lg:justify-between">
            <slot name="batchActions" :checkeds="checkeds"></slot>
            <input v-if="showSearch" type="text" placeholder="Cari disini" class="input input-bordered w-full max-w-xs"
                v-model="queryParams.search" />
        </div>
        <div class="overflow-x-auto">
            <table class="table table-pin-rows relative" v-if="status === 'success'">
                <thead>
                    <tr class="bg-neutral text-white">
                        <th v-if="checked">
                            #
                        </th>
                        <th v-for="(cl, i) in columns" :key="i">{{ cl }}</th>
                    </tr>
                </thead>
                <tbody v-if="data && data.data && data.data.data.length">
                    <tr v-for="(dt, index) in data?.data.data" :key="index">
                        <th v-if="checked">
                            <label>
                                <input type="checkbox" @change="handleChange" class="checkbox"
                                    :checked="checkeds.includes(dt[checked])" :value="dt[checked]" />
                            </label>
                        </th>
                        <slot name="content" :data="dt"></slot>
                    </tr>
                </tbody>
            </table>
            <p class="text-center text-sm mt-5" v-if="status === 'success' && data && data.data && !data.data.data.length">
                Data Kosong
            </p>
            <p class="text-center text-sm mt-5" v-if="status === 'loading'">
                <span class="loading loading-dots loading-lg"></span>
            </p>
        </div>
        <!-- bagian bawah datatable -->
        <div class="flex lg:flex-row flex-col gap-y-5 lg:gap-y-0 lg:justify-between items-center mt-3 overflow-x-auto">
            <select class="select w-full max-w-fit select-bordered" v-model="queryParams.show">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <div class="join text-sm" v-if="status === 'success'">
                <button @click.prevent="handleClickPagination(link.page)"
                    :class="`text-sm join-item btn btn-sm ${link.active ? `btn-active` : ``}`"
                    v-for="(link, index) in data?.data.pagination.options.links" :key="index" v-html="link.label"></button>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
export default {

}
</script>
<style lang="">
    
</style>