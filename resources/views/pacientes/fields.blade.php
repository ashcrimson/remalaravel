<div class="form-row" id="paciente-fields">
    <!-- Run Field -->
    <div class="form-group col-sm-4">

        {!! Form::label('run', 'Run:') !!}

        <div class="input-group ">

            {!! Form::text('run', null, ['id' => 'run','class' => 'form-control','maxlength' => 9]) !!}
            <div class="input-group-append">
                <button class="btn btn-outline-success" type="button" @click="getDatosPaciente()">
                                    <span v-show="!loading">
                                        <i class="fa fa-search"></i>
                                    </span>
                    <span v-show="loading">
                                        <i class="fa fa-sync fa-spin"></i>
                                    </span>
                    Consultar
                </button>
            </div>
        </div>


    </div>

    <!-- Dv Run Field -->
    <div class="form-group col-sm-2">
        {!! Form::label('dv_run', 'Dv Run:') !!}
        {!! Form::text('dv_run', null, ['id' => 'dv_run','class' => 'form-control','maxlength' => 1]) !!}
    </div>

    <div class="form-group col-sm-12" style="padding: 0px; margin: 0px"></div>

    <!-- Primer Nombre Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('primer_nombre', 'Primer Nombre:') !!}
        {!! Form::text('primer_nombre', null, ['id' => 'primer_nombre','class' => 'form-control','maxlength' => 255]) !!}
    </div>

    <!-- Segundo Nombre Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('segundo_nombre', 'Segundo Nombre:') !!}
        {!! Form::text('segundo_nombre', null, ['id' => 'segundo_nombre','class' => 'form-control','maxlength' => 255]) !!}
    </div>

    <!-- Apellido Paterno Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('apellido_paterno', 'Apellido Paterno:') !!}
        {!! Form::text('apellido_paterno', null, ['id' => 'apellido_paterno','class' => 'form-control','maxlength' => 255]) !!}
    </div>

    <!-- Apellido Materno Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('apellido_materno', 'Apellido Materno:') !!}
        {!! Form::text('apellido_materno', null, ['id' => 'apellido_materno','class' => 'form-control','maxlength' => 255]) !!}
    </div>

    <!-- Fecha Nac Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('fecha_nac', 'Fecha Nac:') !!}
        {!! Form::date('fecha_nac', null, ['id' => 'fecha_nac','class' => 'form-control','id'=>'fecha_nac']) !!}
    </div>


    <div class="form-group col-sm-3">
        {!! Form::label('sexo', 'Sexo:') !!}<br>
        <input type="checkbox" data-toggle="toggle" data-size="normal" data-on="M" data-off="F" data-style="ios" name="sexo" id="sexo"
               value="1"
            {{($rema->sexo ?? null)=="M"  ? 'checked' : '' }}>
    </div>

    <div class="form-group col-sm-3">
        {!! Form::label('prevision_id','Previsi??n:') !!}
        {!!
            Form::select(
                'prevision_id',
                select(\App\Models\PacientePrevision::class,'nombre','id')
                , null
                , ['id'=>'previsiones','class' => 'form-control','style'=>'width: 100%']
            )
        !!}
    </div>


    <!-- telefono Field -->
    <div class="form-group col-sm-3">
        {!! Form::label('telefono', 'Tel??fono:') !!}
        {!! Form::text('telefono', null, ['id' => 'telefono','class' => 'form-control','maxlength' => 255]) !!}
    </div>

    <!-- Direccion Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('direccion', 'Direcci??n:') !!}
        {!! Form::text('direccion', null, ['id' => 'direccion','class' => 'form-control','maxlength' => 255]) !!}
    </div>


    <!-- familiar_responsable Field -->
    <div class="form-group col-sm-12">
        {!! Form::label('familiar_responsable', 'Familiar Responsable:') !!}
        {!! Form::text('familiar_responsable', null, ['id' => 'familiar_responsable','class' => 'form-control','maxlength' => 255]) !!}
    </div>

    <!-- Clave Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('clave', 'Clave:') !!}
        {!! Form::text('clave', null, ['class' => 'form-control','maxlength' => 255,'maxlength' => 255]) !!}
    </div>

    <!-- Movil Envia Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('movil_envia', 'Movil Env??a:') !!}
        <label class="checkbox-inline">
            {!! Form::hidden('movil_envia', 0) !!}
            {!! Form::checkbox('movil_envia', '1', null) !!}
        </label>
    </div>

</div>



@push('scripts')
<script>


    const vmPacienteFields = new Vue({
        el: '#paciente-fields',
        name: 'paciente-fields',
        created() {

        },
        data: {
            loading : false,
        },
        methods: {
            async getDatosPaciente(){

                console.log('FN: getDatosPaciente');

                this.loading = true;

                let run = $("#run").val();

                let url = "{{route('get.datos.paciente')}}"+"?run="+run;

                console.log('url',url);

                try{
                    let res = await axios.get(url);
                    let paciente = res.data.data;

                    console.log('respuesta',res);

                    if (!paciente){
                        alertWarning('Rut No Encontrado');
                    }else{
                        $("#dv_run").val(paciente.dv_run);
                        $("#apellido_paterno").val(paciente.apellido_paterno);
                        $("#apellido_materno").val(paciente.apellido_materno);
                        $("#primer_nombre").val(paciente.primer_nombre);
                        $("#segundo_nombre").val(paciente.segundo_nombre);
                        $("#fecha_nac").val(paciente.fecha_nac);

                        if (paciente.sexo=='M'){
                            $('#sexo').bootstrapToggle('on')
                        }else {

                            $("#sexo").bootstrapToggle('off');
                        }

                        $("#sigla_grado").val(paciente.sigla_grado);
                        $("#unid_rep_dot").val(paciente.unid_rep_dot);
                        $("#cond_alta_dot").val(paciente.cond_alta_dot);
                    }


                    this.loading = false;

                }catch (e) {
                    console.log(e.response);
                    alertWarning('Rut No Encontrado');
                    this.loading = false;
                }
            }
        }
    });
</script>
@endpush
