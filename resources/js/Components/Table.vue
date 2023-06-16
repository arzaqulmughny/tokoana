<template>
    <div class="table">
        <table class="table__main">
            <thead>
                <slot name="head" />
            </thead>

            <tbody class="table__body">
                <slot name="body" />
            </tbody>
        </table>
    </div>
</template>

<style lang="scss">
.table {
    overflow-x: auto;
    outline: 1px solid var(--color-4);
    border-radius: 4px;

    &__main {
        min-width: max-content;
        width: 100%;
        background-color: var(--color-5);
    }

    tr {
        &:hover {
            background-color: var(--color-4) !important;
        }

        td {
            padding: 1rem;
            width: fit-content;
            max-width: 20em;
            line-height: 1.5rem;
            vertical-align: top;
            max-width: 30ch;
        }
    }

    thead {
        background-color: var(--color-5) !important;
    }

    tbody {
        tr:nth-child(odd) {
            background-color: var(--color-bg);
        }
    }

    [data-empty="true"] {
        text-align: center;
    }
}
</style>
