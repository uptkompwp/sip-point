<script lang="ts" setup>
import { ChevronDownIcon } from '@heroicons/vue/24/outline';
import { onMounted, ref, toRefs, watch } from 'vue';
import { useRoute } from 'vue-router';
const show = ref<boolean>(false)
const props = defineProps({
    to: Object,
    text: String,
    childrens: Array<{
        text: string,
        to: any
    }>,
    childrenTitle: String
})
const { childrens } = toRefs(props)

const toggleChildren = () => {
    show.value = !show.value
}
const route = useRoute()

const activeMenuChildren = ref<boolean>(false)

const handleCheckCurrentChildren = (routename) => {
    if (routename) {
        (childrens?.value?.some(children => routename.includes(children.to.name))) ? activeMenuChildren.value = true : activeMenuChildren.value = false
        activeMenuChildren.value ? show.value = true : show.value = false
    }

}
watch(route, function () {
    handleCheckCurrentChildren(route.name)
})

onMounted(() => {
    handleCheckCurrentChildren(route.name)
})

</script>
<template>
    <li class="w-full" v-if="!childrens">
        <router-link :to="to!" class="text-[16px] p-3 w-full rounded-lg inline-block">{{ text }}</router-link>
    </li>
    <li v-else class="w-full">
        <button
            :class="`text-[16px] p-3 w-full rounded-lg flex text-left  justify-between items-center ${activeMenuChildren ? `bg-neutral text-white` : ``}`"
            @click="toggleChildren">
            <span>{{ text }}</span>
            <ChevronDownIcon :class="`h-5 w-5 ${show ? `rotate-180` : `rotate-0`}`" />
        </button>
        <Transition name="slide-fade">
            <ul class="menu bg-base-200 mt-2 w-full rounded-box" v-if="show">
                <li>
                    <h2 class="menu-title" v-if="childrenTitle">{{ childrenTitle }}</h2>
                    <ul class="space-y-2">
                        <li v-for="(children, i) in childrens">
                            <router-link v-if="route.name && (children as any).show" :to="children.to ?? '/'"
                                :class="`text-[16px] p-3 w-full rounded-lg inline-block ${(route.name as any).includes(children.to.name) ? `bg-neutral text-white` : ``}`"
                                :key="i">{{
                                    children.text }}</router-link>
                        </li>
                    </ul>
                </li>
            </ul>
        </Transition>
    </li>
</template>
<script lang="ts">
export default {

}
</script>
<style>
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.1s;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(20px);
    opacity: 0;
}
</style>
