<template>

    <div>

        <div>

            <!-- Success Message -->

            <div v-if="success" class="alert alert-success" id="success-alert">
                {{ success }}
            </div>

            <!-- Academic Year Dropdown -->

            <div class="pb-2">

                <label for="academic_year" class="tw-form-label hidden lg:block">
                    Select Academic Year
                </label>

                <select class="tw-form-control w-full" id="academic_year" v-model="academic_year" name="academic_year"
                    @change="showYear">

                    <option disabled value="">
                        Select Academic Year
                    </option>

                    <option v-for="academic in academiclist" :key="academic.id" :value="Number(academic.id)">
                        {{ academic.name }}
                    </option>

                </select>

                <!-- Error -->

                <span v-if="errors.academic_year_id">

                    <p class="text-red-500 text-xs font-semibold">

                        {{ errors.academic_year_id[0] }}

                    </p>

                </span>

            </div>

        </div>

    </div>

</template>

<script>

export default {

    props: [],

    data() {

        return {

            academic_year: '',

            academiclist: [],

            errors: {},

            success: null,
        };
    },

    methods: {

        /*
        |--------------------------------------------------------------------------
        | Change Academic Year
        |--------------------------------------------------------------------------
        */

        showYear() {
            this.errors = {};

            this.success = null;

            let formData = new FormData();

            formData.append(
                'academic_year_id',
                this.academic_year
            );

            axios.post(
                '/admin/academicyear/index',
                formData,
                {
                    headers: {
                        'Content-Type':
                            'multipart/form-data'
                    }
                }
            )

                .then(response => {

                    this.success =
                        response.data.message;

                    /*
                    |--------------------------------------------------------------------------
                    | Update Selected Value
                    |--------------------------------------------------------------------------
                    */

                    if (
                        response.data.current_year
                    ) {

                        this.academic_year =
                            Number(
                                response.data.current_year.id
                            );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Reload
                    |--------------------------------------------------------------------------
                    */

                    setTimeout(() => {

                        window.location.reload();

                    }, 500);

                })

                .catch(error => {

                    if (
                        error.response &&
                        error.response.data.errors
                    ) {

                        this.errors =
                            error.response.data.errors;
                    }
                });
        },

        /*
        |--------------------------------------------------------------------------
        | Load Academic Years
        |--------------------------------------------------------------------------
        */

        getAcademicYear() {
            axios.get('/admin/list/academicyear')

                .then(response => {

                    this.academiclist =
                        response.data.academiclist || [];

                    /*
                    |--------------------------------------------------------------------------
                    | Current Academic Year
                    |--------------------------------------------------------------------------
                    */

                    if (
                        response.data.current_year &&
                        response.data.current_year.id
                    ) {

                        this.academic_year =
                            Number(
                                response.data.current_year.id
                            );

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Fallback First Option
                        |--------------------------------------------------------------------------
                        */

                        if (this.academiclist.length > 0) {

                            this.academic_year =
                                Number(
                                    this.academiclist[0].id
                                );
                        }
                    }
                })

                .catch(error => {

                    console.log(error);

                });
        },
    },

    created() {
        this.getAcademicYear();
    }
};

</script>
