<template>
    <div class="flex items-center justify-between main-header">
        <div class="flex items-center">
            <el-icon :size="18" class="cursor-pointer transition-all"
                     :class="{'rotate-180':menuInfo.isMobile?!menuInfo.isSlider:menuInfo.isCollapse}"
                     @click="toggleCollapse">
                <component is="fold"/>
            </el-icon>

            <div class="ml-3" v-if="!menuInfo.isMobile">
                <AMisRenderer :amisJson="tool.left"/>
            </div>
            <div v-else class="ml-3">
                <el-button text @click="showHeaderToolbar=true">工具栏</el-button>
            </div>
        </div>
        <div class="flex items-center">
            <div v-if="!menuInfo.isMobile">
                <AMisRenderer :amisJson="tool.right"/>
            </div>
        </div>
        <el-drawer v-model="showHeaderToolbar" v-if="menuInfo.isMobile" direction="ttb"
                   :with-header="false">
            <div>
                <AMisRenderer :amisJson="tool.left"/>
            </div>
            <div>
                <AMisRenderer :amisJson="tool.right"/>
            </div>
        </el-drawer>
    </div>
</template>

<script setup lang="ts">
import {useMenuStore} from "@/stores/menu";
import {useRouter} from "vue-router";
import {onMounted, reactive, ref} from "vue";
import {useGetToolbar} from "@/utils/api";
import AMisRenderer from "@/components/amis/AMisRenderer.vue";
import {ElButton, ElDrawer, ElIcon} from "element-plus";

const {toggleCollapse, menuInfo} = useMenuStore()
const router = useRouter()
const logout = () => {
    router.push({name: "login", replace: true,})
}

const showHeaderToolbar = ref(false)

const tool = reactive({
    left: {},
    right: {}
})

const getToolbar = async () => {
    const res = await useGetToolbar()
    tool.left = res.data.left
    tool.right = res.data.right
}

onMounted(() => {
    getToolbar()
})

</script>
