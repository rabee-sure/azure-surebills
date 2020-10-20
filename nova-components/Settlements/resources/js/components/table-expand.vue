<template>
    <div>
        <Row class="expand-row">
            <Col span="24">
                <span class="expand-key">Id: </span>
                <span class="expand-value">{{ row.id }}</span>
            </Col>
        </Row>
        <Row class="expand-row">
            <Col span="8">
                <span class="expand-key">balance: </span>
                <span class="expand-value">{{ row.balance }}</span>
            </Col>
            <Col span="8">
                <span class="expand-key">CARD : </span>
                <span class="expand-value">{{ row.card }}</span>
            </Col>
            <Col span="8">
                <span class="expand-key">CARD BRAND: </span>
                <span class="expand-value">{{ row.card_brand }}</span>
            </Col>
        </Row>
        <Row>
            <Col span="8">
                <span class="expand-key">Auth id: </span>
                <span class="expand-value">《{{ row.auth_id }}》</span>
            </Col>
            <Col span="8">
                <span class="expand-key">Reference: </span>
                <span class="expand-value">{{ row.reference }}</span>
            </Col>
            <Col span="8">
                <span class="expand-key">Receipt: </span>
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