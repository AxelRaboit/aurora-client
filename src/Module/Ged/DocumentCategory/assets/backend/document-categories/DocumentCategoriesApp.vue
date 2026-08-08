<script setup>
/**
 * Layer 5 of the client extension — reference example.
 *
 * Aurora's component is not touched. This wrapper sits at
 * `src/Module/Ged/DocumentCategory/assets/backend/document-categories/`, which
 * the app.js glob flattens to the same key Aurora publishes
 * (`ged/backend/document-categories/DocumentCategoriesApp`). Client modules are
 * spread after Aurora's, so this one wins — no Twig override to write.
 *
 * `extraFields` declares the field; the composable merges it into both forms
 * and submits it, because the request body is the whole form object. The three
 * scoped slots place it in the table and in the two modals.
 */
import AppColorField from "@shared/components/form/picker/AppColorField.vue";
import AuroraDocumentCategoriesApp from "@ged/backend/document-categories/DocumentCategoriesApp.vue";

const extraFields = {
    color: {
        default: "",
        fromEntity: (category) => category.color ?? "",
    },
};
</script>

<template>
    <AuroraDocumentCategoriesApp v-bind="$attrs" :extra-fields="extraFields">
        <template #extra-headers>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted hidden lg:table-cell">
                Couleur
            </th>
        </template>

        <template #extra-cells="{ category }">
            <td class="px-6 py-3 hidden lg:table-cell">
                <span v-if="category.color" class="inline-flex items-center gap-2">
                    <span class="w-4 h-4 rounded border border-line" :style="{ backgroundColor: category.color }" />
                    <span class="font-mono text-xs text-muted">{{ category.color }}</span>
                </span>
                <span v-else class="text-muted">—</span>
            </td>
        </template>

        <template #extra-form-fields="{ form, errors }">
            <AppColorField v-model="form.color" label="Couleur" :error="errors.color" />
        </template>
    </AuroraDocumentCategoriesApp>
</template>
