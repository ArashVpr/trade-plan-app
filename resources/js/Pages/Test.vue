
<template>
    <div class="card flex justify-center">
        <Toast position="top-center" group="headless" @close="visible = false">
            <template #container="{ message, closeCallback }">
                <section class="flex flex-col p-4 gap-4 w-full bg-primary/70 rounded-xl">
                    <div class="flex items-center gap-5">
                        <i class="pi pi-cloud-upload text-white dark:text-black text-2xl"></i>
                        <span class="font-bold text-base text-white dark:text-black">{{ message.summary }}</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <ProgressBar :value="progress" :showValue="false" :style="{ height: '4px' }" pt:value:class="!bg-primary-50 dark:!bg-primary-900" class="!bg-primary/80"></ProgressBar>
                        <label class="text-sm font-bold text-white dark:text-black">{{ progress }}% uploaded</label>
                    </div>
                    <div class="flex gap-4 mb-4 justify-end">
                        <Button label="Another Upload?" size="small" @click="closeCallback"></Button>
                        <Button label="Cancel" size="small" @click="closeCallback"></Button>
                    </div>
                </section>
            </template>
        </Toast>
        <Button @click="show" label="View" />
    </div>
</template>

<script setup>
import { useToast } from "primevue/usetoast";
import { ref, onUnmounted } from 'vue';
const toast = useToast();
const visible = ref(false);
const progress = ref(0);
const interval = ref();

onUnmounted(() => {
    if (interval.value) {
        clearInterval(interval.value);
    }
})

const show = () => {
    if (!visible.value) {
        toast.add({ severity: 'custom', summary: 'Uploading your files.', group: 'headless', styleClass: 'rounded-2xl' });
        visible.value = true;
        progress.value = 0;

        if (interval.value) {
            clearInterval(interval.value);
        }

        interval.value = setInterval(() => {
            if (progress.value <= 100) {
                progress.value = progress.value + 20;
            }

            if (progress.value >= 100) {
                progress.value = 100;
                clearInterval(interval.value);
            }
        }, 1000);
    }
};
</script>
