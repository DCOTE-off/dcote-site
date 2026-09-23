import { reactive } from 'vue';

/**
 * Общий стейт для виджетов рейтинга: одновременно открыт максимум один попап.
 * openId — «type:id» открытого виджета, либо null.
 */
export const ratingStore = reactive({
    openId: null,
});
