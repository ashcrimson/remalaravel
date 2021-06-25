<?php

namespace App\Http\Controllers;

use App\DataTables\RemaDataTable;
use App\DataTables\Scopes\ScopeRemaDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateRemaRequest;
use App\Http\Requests\UpdateRemaRequest;
use App\Models\Paciente;
use App\Models\PacienteAtencion;
use App\Models\Rema;
use App\Models\RemaEstado;
use Carbon\Carbon;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;

class RemaController extends AppBaseController
{
    /**
     * Display a listing of the Rema.
     *
     * @param RemaDataTable $remaDataTable
     * @return Response
     */
    public function index(RemaDataTable $remaDataTable,Request $request)
    {

        $scope = new ScopeRemaDataTable();
        $scope->del = $request->del ?? null;
        $scope->al = $request->al ?? null;

        $remaDataTable->addScope($scope);

        return $remaDataTable->render('remas.index');
    }

    /**
     * Show the form for creating a new Rema.
     *
     * @return Response
     */
    public function create()
    {
        $rema = $this->getRemaTemporal();

        return redirect(route('remas.edit',$rema->id));

    }

    /**
     * Store a newly created Rema in storage.
     *
     * @param CreateRemaRequest $request
     *
     * @return Response
     * @throws \Exception
     */
    public function store(CreateRemaRequest $request)
    {


        try {
            DB::beginTransaction();

            $this->procesaStore($request);

        } catch (\Exception $exception) {
            DB::rollBack();

            throw new \Exception($exception);
        }

        DB::commit();

        Flash::success('Rema saved successfully.');

        return redirect(route('remas.index'));
    }

    public function procesaStore(Request $request)
    {

//        DB::enableQueryLog();

        /**
         * @var  Paciente $paciente
         */
        $paciente = $this->creaOactualizaPaciente($request);

        $request->merge([
            'user_id' => auth()->user()->id,
            'paciente_id' => $paciente->id,
            'hora_de_llamada' => timeToDateTime($request->hora_de_llamada),
            'hora_de_salida' => timeToDateTime($request->hora_de_salida),
            'hora_de_llegada' => timeToDateTime($request->hora_de_llegada),
            'estado_id' => RemaEstado::CREADA,
        ]);


        /** @var Rema $rema */
        $rema = Rema::create($request->all());

        $request->merge([
            'rema_id' => $rema->id,
        ]);


//        dd(DB::getQueryLog());

    }

    /**
     * Display the specified Rema.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Rema $rema */
        $rema = Rema::find($id);

        if (empty($rema)) {
            Flash::error('Rema not found');

            return redirect(route('remas.index'));
        }

        return view('remas.show')->with('rema', $rema);
    }

    /**
     * Show the form for editing the specified Rema.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        /** @var Rema $rema */
        $rema = Rema::find($id);


        if (empty($rema)) {
            Flash::error('Rema not found');

            return redirect(route('remas.index'));
        }

        return view('remas.edit')->with('rema', $rema);
    }

    /**
     * Update the specified Rema in storage.
     *
     * @param int $id
     * @param UpdateRemaRequest $request
     *
     * @return Response
     * @throws \Exception
     */
    public function update($id, UpdateRemaRequest $request)
    {

        /** @var Rema $rema */
        $rema = Rema::find($id);



        if (empty($rema)) {
            Flash::error('Rema not found');

            return redirect(route('remas.index'));
        }


        try {
            DB::beginTransaction();

            $this->procesaUpdate($request,$rema);

        } catch (\Exception $exception) {
            DB::rollBack();

            throw new \Exception($exception);
        }

        DB::commit();

        Flash::success('Rema updated successfully.');

        return redirect(route('remas.index'));
    }

    public function procesaUpdate(Request $request,Rema $rema)
    {


        //        DB::enableQueryLog();

        /**
         * @var  Paciente $paciente
         */
        $paciente = $this->creaOactualizaPaciente($request);

        $request->merge([
            'paciente_id' => $paciente->id,
            'hora_de_llamada' => timeToDateTime($request->hora_de_llamada),
            'hora_de_salida' => timeToDateTime($request->hora_de_salida),
            'hora_de_llegada' => timeToDateTime($request->hora_de_llegada),
            'estado_id' => RemaEstado::CREADA,
        ]);


        /** @var Rema $rema */
        $rema->fill($request->all());
        $rema->save();

        return $rema;

//        dd(DB::getQueryLog());
    }

    /**
     * Remove the specified Rema from storage.
     *
     * @param  int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Rema $rema */
        $rema = Rema::find($id);

        if (empty($rema)) {
            Flash::error('Rema not found');

            return redirect(route('remas.index'));
        }

        $rema->delete();

        Flash::success('Rema deleted successfully.');

        return redirect(route('remas.index'));
    }

    public function creaOactualizaPaciente(Request $request)
    {
        $paciente = Paciente::updateOrCreate([
            'run' => $request->run,
            'dv_run' => $request->dv_run,

        ],[
            'run' => $request->run,
            'fecha_nac' => $request->fecha_nac,
            'dv_run' => $request->dv_run,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'primer_nombre' => $request->primer_nombre,
            'segundo_nombre' => $request->segundo_nombre,

            'sexo' => $request->sexo ? 'M' : 'F',

            'direccion' => $request->direccion,
            'familiar_responsable' => $request->familiar_responsable,
            'telefono' => $request->telefono,
            'telefono2' => $request->telefono2,
            'prevision_id' => $request->prevision_id,

        ]);



        return $paciente;
    }



    public function getRemaTemporal()
    {
        $rema = Rema::where('user_id',auth()->user()->id)->where('estado_id',RemaEstado::TEMPORAL)->first();

        if (!$rema){
            $rema = Rema::create([
                'user_id' => auth()->user()->id,
                'estado_id' => RemaEstado::TEMPORAL,
            ]);
        }

        return $rema;
    }
}
