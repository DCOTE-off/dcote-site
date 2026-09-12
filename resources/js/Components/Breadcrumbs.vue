<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useScrollable } from '../Composables/useScrollable';

defineProps({
    items: {
        type: Array,
        default: () => [],
        required: true,
    },
});

const wrapper = ref(null);
const { isScrollable } = useScrollable(wrapper);
</script>
<template>
    <nav aria-label="breadcrumbs" class="breadcrumbs">
        <ol class="breadcrumbs__list">
            <div
                ref="wrapper"
                class="breadcrumbs__scroll-wrapper"
                :class="{ 'is-scrollable': isScrollable }"
            >
                <li v-for="(item,index) in items" :key="item.href" class="breadcrumbs__item">
                    <Link v-if="index !== items.length - 1" :href="item.href">{{ item.text }}</Link>
                    <span aria-current="page" v-else>{{ item.text }}</span>
                </li>
            </div>
        </ol>
    </nav>
</template>