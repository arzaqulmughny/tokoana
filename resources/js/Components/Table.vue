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
    border-radius: 5px;

    &__main {
        width: 100%;
    }

    tr {
        &:hover {
            background-color: var(--color-4) !important;

            .item-action {
                visibility: visible;
            }
        }

        td {
            padding: 1.2rem;
            width: fit-content;
            max-width: 20em;
            line-height: 1.5rem;
            vertical-align: top;

            input[type="checkbox"] {
                cursor: pointer;
            }
        }
    }

    tr:nth-child(even) {
        background-color: var(--color-5);
    }

    thead {
        background-color: var(--color-5);
    }

    [data-empty="true"] {
        text-align: center;
    }
}
</style>
