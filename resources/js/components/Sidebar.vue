<script lang="ts" setup>
import { useMenuStore } from '../stores/menuStore'
import { useRoute, RouteLocationRaw } from 'vue-router'
import { watch, onMounted, onBeforeMount, reactive, getCurrentInstance } from 'vue'
// vcriables
const menuStore = useMenuStore()
const mediaQuery = window.matchMedia("(min-width:1024px)")
const route = useRoute();
const { auth } = getCurrentInstance()?.appContext.app as unknown as { auth: any }
// function
const handleClickOverley = () => {
    menuStore.setShowMenu(false)
}
const onResize = () => {
    !mediaQuery.matches ? menuStore.setIsMobile(true) : menuStore.setIsMobile(false)
    menuStore.getIsMobile ? menuStore.setShowMenu(false) : menuStore.setShowMenu(true)
}

// lifecycle
onMounted(() => {
    onResize()
    window.addEventListener('resize', onResize)
})

onBeforeMount(() => {
    window.removeEventListener('resize', onResize);
})


watch(route, function () {
    if (menuStore.getIsMobile) {
        menuStore.setShowMenu(false)
    }
}, { immediate: true })

const childrensMaster = reactive<{ text: string, to: RouteLocationRaw, show: boolean }[]>([
    {
        text: "Kelas",
        to: {
            name: "kelas"
        },
        show: true
    },
    {
        text: "Mata Kuliah",
        to: {
            name: "makul"
        },
        show: true
    },
    {
        text: "Mahasiswa",
        to: {
            name: "mahasiswa"
        },
        show: true
    },
    {
        text: "Asisten",
        to: {
            name: "asisten"
        },
        show: auth.check("ADMIN")
    },
])

// console.log(childrensMaster)
// const childrensPoint = reactive<{ text: string, to: RouteLocationRaw }[]>([
//     {
//         text: "Sesi Pertemuan",
//         to: {
//             name: "sesi"
//         }
//     },
//     // {
//     //     text: "Kuis",
//     //     to: {
//     //         name: "kuis"
//     //     }
//     // },
//     // {
//     //     text: "Point",
//     //     to: {
//     //         name: "sesi"
//     //     }
//     // },
// ])


</script>
<template>
    <TransitionGroup name="slide">
        <aside class="w-[250px] fixed bg-white top-0 bottom-0 p-[0.5rem] border-r z-[999] overflow-y-auto"
            v-if="menuStore.getShowMenu">
            <Logo />
            <ul class="space-y-2 mt-3 px-[0.4rem]">
                <MenuLink :to="{ name: 'home' }" text="Dashboard" />
                <MenuLink :to="{ name: 'sesi' }" text="Point Managements" />
                <!-- <MenuLink text="Points Managements" :childrens="childrensPoint" /> -->
                <MenuLink text="Master" :childrens="childrensMaster" />
                <MenuLink :to="{ name: 'reports' }" text="Reports" />
            </ul>
            <!-- copyright -->
            <div class="absolute bottom-0 right-0 left-0 bg-base-200 text-sm text-center p-2 font-mono border-t">
                &copy; UPT KOMP {{ new Date().getFullYear() }}
            </div>
        </aside>
        <div v-if="menuStore.getIsMobile && menuStore.getShowMenu"
            class="bg-neutral opacity-20 fixed top-0 left-0 right-0 bottom-0 cursor-pointer z-[888]"
            @click="handleClickOverley">
        </div>
    </TransitionGroup>
</template>
<script lang="ts">
import MenuLink from './MenuLink.vue';
import Logo from './Logo.vue';
export default {
    component: {
        MenuLink
    }
}
</script>
<style scoped>
.slide-enter-active {
    transition: all 0.1s ease-out;
}

.slide-leave-active {
    transition: all 0.1s ease-in;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(-10px);
    opacity: 0;
}
</style>