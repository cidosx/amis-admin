<template>
    <div ref="el"></div>
</template>

<script setup lang="ts">
import {onMounted, ref, watch} from "vue";
import {useRoute, useRouter} from "vue-router";
import {ElMessage, ElNotification, ElMessageBox} from 'element-plus'
import axios from "axios";
//@ts-ignore
const amis = window.amisRequire('amis/embed');
const el = ref<HTMLInputElement | null>(null)

const props = defineProps<{
    amisJson: object,
}>()

const router = useRouter()
const route = useRoute()

let amisScoped;

watch(() => props.amisJson, (json) => {
    build()
})


const build = () => {

    amisScoped = amis.embed(el.value, props.amisJson, {}, {
            fetcher: ({
                          url, // 接口地址
                          method, // 请求方法 get、post、put、delete
                          data, // 请求数据
                          responseType,
                          config, // 其他配置
                          headers // 请求头
                      }: any) => {
                config = config || {};
                config.withCredentials = true;
                responseType && (config.responseType = responseType);

                if (config.cancelExecutor) {
                    config.cancelToken = new (axios as any).CancelToken(
                        config.cancelExecutor
                    );
                }
                config.headers = headers || {};
                config.headers['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (method !== 'post' && method !== 'put' && method !== 'patch') {
                    if (data) {
                        config.params = data;
                    }
                    return (axios as any)[method](url, config);
                } else if (data && data instanceof FormData) {
                    config.headers = config.headers || {};
                    config.headers['Content-Type'] = 'multipart/form-data';
                } else if (
                    data &&
                    typeof data !== 'string' &&
                    !(data instanceof Blob) &&
                    !(data instanceof ArrayBuffer)
                ) {
                    data = JSON.stringify(data);
                    config.headers = config.headers || {};
                    config.headers['Content-Type'] = 'application/json';
                }
                return (axios as any)[method](url, data, config);
            },
            jumpTo: (to: string) => {
                if (to == "back()") {
                    router.back()
                    return
                }
                //检测http://或者https://
                if (to.indexOf('http://') == 0 || to.indexOf('https://') == 0) {
                    window.location.href = to
                    return
                }
                router.push(to)
            },
            updateLocation: (to: string, replace: boolean) => {
                //过滤query参数
                const queryArr = to.split('&').filter(item => {
                    return item.indexOf('search[') === -1
                });
                let query = queryArr.join('&')
                //判断是否?开头
                if (query.indexOf('?') !== 0) {
                    query = '?' + query
                }
                const path = route.path + query
                if (replace) {
                    router.replace(path)
                } else {
                    router.push(path)
                }
            },
            notify: (type: any, msg: string) => {

                let showType: 'message' | 'alert' | 'notice'
                let showContent: string
                try {
                    const msgData = JSON.parse(msg)
                    showType = msgData.type
                    showContent = msgData.content
                } catch (e) {
                    showType = "message"
                    showContent = msg
                }
                if (showType == "alert") {
                    ElMessageBox({
                        message: showContent,
                        title: "提示",
                        type: type,
                        center: true
                    })
                } else if (showType == "message") {
                    ElMessage({
                        message: showContent,
                        type: type,
                    })
                } else {
                    ElNotification({
                        message: showContent,
                        dangerouslyUseHTMLString: true,
                        type: type,
                    })
                }


            },
            alert: (content: string) => {
                ElMessage(content)
            },
            confirm: (content: string) => {
                return ElMessageBox.confirm(content, "提示", {
                    cancelButtonText: "取消",
                    confirmButtonText: "确定",
                    dangerouslyUseHTMLString: true,
                }).then(() => {
                    return true;
                }).catch(() => {

                })
            },
            affixOffsetTop: 48,
        }
    );
}

onMounted(build)

</script>
