<template>
    <div>
        <Row class="expand-row" v-if="row.id">
            <Col span="24">
                <span class="expand-key">{{ __('ID') }}: </span>
                <span class="expand-value">{{ row.id }}</span>
            </Col>
        </Row>
        <Row class="expand-row">
            <Col span="8"  v-if="row.balance">
                <span class="expand-key">{{ __('Balance') }}: </span>
                <span class="expand-value">{{ row.balance }}</span>
            </Col>
            <Col span="8" v-if="row.card">
                <span class="expand-key">{{ __('Card Number') }} : </span>
                <span class="expand-value">{{ row.card }}</span>
            </Col>
            <Col span="8" v-if="row.card_brand">
                <span class="expand-key">{{ __('Card Type') }}: </span>
                <span class="expand-value">{{ row.card_brand }}</span>
            </Col>
        </Row>
        <Row>
            <Col span="8" v-if="row.auth_id">
                <span class="expand-key">{{ __('Auth ID') }}: </span>
                <span class="expand-value">《{{ row.auth_id }}》</span>
            </Col>
            <Col span="8" v-if="row.reference">
                <span class="expand-key">{{ __('Reference') }}: </span>
                <span class="expand-value">{{ row.reference }}</span>
            </Col>
            <Col span="8" v-if="row.receipt">
                <span class="expand-key">{{ __('Receipt') }}: </span>
                <span class="expand-value">{{ row.receipt }}</span>
            </Col>
        </Row>
    </div>
</template>

<script>
    export default {
        props: {
            row: Object
        }
    };
</script>


<style scoped>
    .expand-row{
        margin-bottom: 16px;
    }
</style>