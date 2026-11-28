import { useState, type FormEvent} from 'react'
import { useForm } from '@inertiajs/react';
import { ShieldCheck, Check, CircleHelp, Plus, Save, Trash2, ChevronRight, ChevronDown } from 'lucide-react'
import AppLayout from '@/layouts/AppLayout';
import { SectionHeading } from '@/components/ui/SectionHeading';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import Textarea from '@/components/ui/Textarea';
import { type Tema, type Oposicion, type Pregunta, type Test } from '@/types/models';
import { TestForm } from '@/pages/admin/tests/create';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import InputError from '@/components/input-error';

type TestEditable = Test & {
    tema_id: string;
    preguntasOpciones: Pregunta[];
};

const MIN_PREGUNTAS = 5, MAX_PREGUNTAS = 15;
const MIN_OPCIONES = 3, MAX_OPCIONES = 4;


export default function TestsEdit(
    { oposicion, temario, test }: 
    { oposicion: Oposicion, temario: Tema[], test: TestEditable }
) {
    /** 
     * Formulario completo de edición
     * Datos iniciales los antiguos 
     */
    const { data, setData, patch, processing, errors, clearErrors } = 
        useForm<TestForm>({
            titulo: test.titulo, 
            descripcion: test.descripcion, 
            tema_id: test.tema_id, 
            preguntasOpciones: test.preguntasOpciones  
        });


    function handleSubmit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        patch(route('admin.tests.update', test.id));
    }


    /** Dropdown de temas */
    const [openDropdown, setOpenDropdown] = useState(false);


    /**
     * @returns Estado inicial de una pregunta:
     * 3 opciones sin contenido, la primera correcta 
     */
    function crearPregunta(): Pregunta {
        return {
            enunciado: '',
            opciones: [
                { contenido: '', es_correcta: true },
                { contenido: '', es_correcta: false },
                { contenido: '', es_correcta: false },
            ],
        };
    }




    // ----- Gestión de errores
    // Errores de Laravel como un objeto de claves de texto,
    // por ejemplo "preguntasOpciones.0.enunciado"
    type ErroresLaravel = { [clave: string]: string };

    function errorDe(clave: string): string | undefined {
        const erroresLaravel: ErroresLaravel = errors;

        return erroresLaravel[clave];
    }

    // Quita el error de un campo por su clave, por ejemplo "preguntasOpciones.0.enunciado"
    function limpiarErrorDe(clave: string) {
        if (!errorDe(clave)) return;
        clearErrors(clave as keyof TestForm);
    }

    // ----- Gestión de tema seleccionado
    const selectedTema = temario.find((tema) => (tema.id === data.tema_id));

    // ----- Gestión de preguntas
    const preguntas = data.preguntasOpciones;

    function updatePregunta(indice: number, cambios: Partial<Pregunta>) {  // Partial hace opcionales todas las props de Pregunta
        // Copia de la lista de preguntas
        const copiaPreguntas = [...preguntas];
        
        // Pregunta a modificar
        const preguntaActual = preguntas[indice];

        // Actualizar pregunta: datos actuales + los cambios encima
        // (cuando una propiedad aparece en los dos objetos, gana la última) 
        const preguntaNueva = { ...preguntaActual, ...cambios };

        // Colocar pregunta actualizada en su posición
        copiaPreguntas[indice] = preguntaNueva;

        // Actualizar formulario
        setData('preguntasOpciones', copiaPreguntas);
    }

    function addPregunta() {
        if (preguntas.length >= MAX_PREGUNTAS) return;
        setData('preguntasOpciones', [...preguntas, crearPregunta()]);
    }

    function deletePregunta(indice: number) {
        if (preguntas.length <= MIN_PREGUNTAS) return;
    
        clearErrors();

        // Guarda todas las preguntas actuales menos la pregunta elegida, según su índice
        setData('preguntasOpciones', preguntas.filter((_, i) => i !== indice));
    }


    // ----- Gestión de opciones de una pregunta

    function addOpcion(indice: number) {
        // opciones de la pregunta, segun indice de pregunta
        const opciones = preguntas[indice].opciones;

        if (opciones.length >= MAX_OPCIONES) return;
        
        updatePregunta(indice, { opciones: [...opciones, { contenido: '', es_correcta: false }] });
    }

    function deleteOpcion(indice: number, indiceOpcion: number) {
        const opciones = preguntas[indice].opciones;

        if (opciones.length <= MIN_OPCIONES) return;

        clearErrors();

        const restantes = opciones.filter((_, i) => i !== indiceOpcion);

        if (!restantes.some((o) => o.es_correcta)) 
            restantes[0] = { ...restantes[0], es_correcta: true };
        
        updatePregunta(indice, { opciones: restantes });
    }

    function updateTextoOpcion(indice: number, indiceOpcion: number, contenido: string) {
        const opciones = preguntas[indice].opciones.map((opc, i) =>
            i === indiceOpcion ? { ...opc, contenido } : opc,
        );

        updatePregunta(indice, { opciones });

        limpiarErrorDe(`preguntasOpciones.${indice}.opciones.${indiceOpcion}.contenido`);
    }

    function marcarCorrecta(indice: number, indiceOpcion: number) {
        const opciones = preguntas[indice].opciones.map((opc, i) => ({
            ...opc,
            es_correcta: i === indiceOpcion,
        }));

        updatePregunta(indice, { opciones });

        limpiarErrorDe(`preguntasOpciones.${indice}.opciones`);
    }


    return (
        <AppLayout headTitle='Tests | Proposite'>
            <div className="mx-auto w-full max-w-6xl px-4 py-6 flex flex-col gap-10">
                <div>
                    <SectionHeading 
                        as='h1' 
                        eyebrow={`Editar test · ${oposicion.nombre}`} 
                        title='Edición de test' 
                        size='lg'
                        eyebrowIcon={
                            <ShieldCheck className="md:size-4 size-6 text-secondary" />
                        }
                    />

                    <p className="mt-2 text-sm text-neutral-foreground">
                        Prepara preguntas claras y marca la respuesta correcta para que la comunidad pueda practicar.
                    </p>
                </div>

                <div className="bg-tertiary flex flex-wrap items-center gap-2 rounded-xl border border-border 
                bg-background p-4 text-sm text-neutral-foreground">
                    <CircleHelp className="size-4 shrink-0 text-primary" />
                    <span>
                        El test debe incluir entre <strong className="text-foreground">5 y 15 preguntas</strong>. Cada
                        pregunta necesita entre <strong className="text-foreground">3 y 4 opciones</strong> y una
                        respuesta correcta.
                    </span>
                </div>
                
                <form 
                    onSubmit={handleSubmit} 
                    noValidate 
                    className='flex flex-col gap-3'
                >
                    <section className="flex flex-col gap-3 mb-10 p-5 sm:p-6 rounded-xl border border-border bg-white">
                        <SectionHeading 
                            as='h2' 
                            eyebrow={``} 
                            title='Información del test' 
                            size='md'
                        />
                        <p className="mb-2 text-sm text-neutral-foreground">
                            Define el título y el tema al que pertenece.
                        </p>

                        <div className="grid gap-5 md:grid-cols-2 md:grid-rows-[auto_1fr] items-stretch">

                            {/* TITULO */}
                            <div className="flex flex-col gap-2 text-sm text-foreground md:col-start-1 md:row-start-1">
                                <Label htmlFor="test-titulo" className="mb-2 block font-semibold">
                                    Título del test <span className="text-error-foreground">*</span>
                                </Label>
                                
                                <Input
                                    id="test-titulo"
                                    type='text'
                                    value={data.titulo}
                                    disabled={processing}
                                    errors={errors.titulo ? true : false}
                                    onChange={(event) => {
                                        setData('titulo', event.target.value);
                                        clearErrors('titulo');
                                    }}
                                    className={`bg-background ${errors.titulo ? `border-error-foreground! 
                                    placeholder:text-error-foreground! focus:border-2!` : ''}`}
                                    placeholder="Ej. Organización del Estado: conceptos clave"
                                />

                                <InputError className='!font-normal' message={errors.titulo} />
                            </div>

                            {/* TEMA */}
                            <div className="flex flex-col gap-2 text-sm text-foreground md:col-start-1 md:row-start-2">
                                <Label htmlFor="test-temario" className="mb-2 block font-semibold">
                                    Tema asociado <span className="text-error-foreground">*</span>
                                </Label>

                                <DropdownMenu open={openDropdown} onOpenChange={setOpenDropdown}>
                                    <DropdownMenuTrigger asChild>
                                        <button
                                            type="button" 
                                            disabled={processing} 
                                            aria-label="Selecciona un tema disponible"
                                            aria-invalid={Boolean(errors.tema_id)}
                                            className={`inline-flex justify-between items-center px-3 py-2 w-full 
                                            appearance-none rounded-sm border border-border outline-none 
                                            transition focus:border-primary focus:ring-2 focus:ring-ring/30 
                                            disabled:cursor-not-allowed disabled:opacity-50 bg-background
                                            ${errors.tema_id ? `border-error-foreground! placeholder:text-error-foreground! 
                                            focus:border-2!` : ''}`}
                                        >
                                            <div className={`text-left ${errors.tema_id ? `text-error-foreground` : ''}`}>
                                                {selectedTema 
                                                ? (
                                                    <p className={`text-foreground ${errors.tema_id ? `text-error-foreground` : ''}`}>
                                                        <strong>{selectedTema.bloque}</strong>{'. '}
                                                        <strong> Tema {selectedTema.numero_tema}{'. '}</strong>
                                                        {selectedTema.titulo}
                                                    </p>
                                                ) : (
                                                    <span className={`text-neutral-foreground ${errors.tema_id ? `text-error-foreground` : ''}`}>
                                                        Selecciona un tema disponible
                                                    </span>
                                                )}
                                            </div>

                                            {openDropdown 
                                            ? (
                                                <ChevronRight 
                                                    className={`text-foreground size-5 ${errors.tema_id ? `text-error-foreground` : ''}`}
                                                    strokeWidth={1.5} 
                                                />
                                            ) : (
                                                <ChevronDown 
                                                    className={`text-foreground size-5 ${errors.tema_id ? `text-error-foreground` : ''}`}
                                                    strokeWidth={1.5} 
                                                />
                                            )}
                                        </button>
                                    </DropdownMenuTrigger>
                                    
                                    <DropdownMenuContent 
                                        className="bg-background border border-border rounded-sm
                                            overflow-y-auto overflow-x-hidden
                                            max-h-72
                                            data-[state=open]:zoom-in-100
                                            data-[side=bottom]:slide-in-from-top-0
                                            data-[side=top]:slide-in-from-bottom-0"
                                        align="center"
                                    >
                                        {temario.map((tema) => (
                                            <DropdownMenuItem 
                                                className='text-foreground text-sm hover:bg-tertiary focus:bg-tertiary 
                                                focus:text-tertiary-foreground'
                                                key={tema.id} 
                                                onSelect={() => {
                                                    setData('tema_id', tema.id);
                                                    clearErrors('tema_id');
                                                }}
                                            >
                                                <p>
                                                    <strong>{tema.bloque}</strong>{'. '}
                                                    <strong>Tema {tema.numero_tema}{'. '}</strong>
                                                    {tema.titulo}
                                                </p>
                                            </DropdownMenuItem>
                                        ))}
                                    </DropdownMenuContent>
                                </DropdownMenu>

                                <InputError className='!font-normal' message={errors.tema_id} />
                            </div>

                            {/* DESCRIPCION */}
                            <div className="flex flex-col gap-2 text-sm text-foreground md:col-start-2 md:row-start-1 md:row-span-2">
                                <Label htmlFor="test-descripcion" className="mb-2 block font-semibold">
                                    Descripción <span className="text-error-foreground">*</span>
                                </Label>

                                <Textarea
                                    id="test-descripcion"
                                    value={data.descripcion}
                                    onChange={(event) => {
                                        setData('descripcion', event.target.value);
                                        clearErrors('descripcion');
                                    }}
                                    errors={errors.descripcion ? true : false}
                                    placeholder="Describe lo que el opositor pondrá a prueba en este test..."
                                    disabled={processing}
                                    className={`bg-background w-full flex-1 resize-none ${errors.descripcion ? 
                                    `placeholder:text-error-foreground! focus:border-2!` : ''}`}
                                />

                                <InputError className='!font-normal' message={errors.descripcion} />
                            </div>
                        </div>
                    </section>


                    <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                        <SectionHeading 
                            as='h2' 
                            eyebrow="Contenido" 
                            title='Preguntas del test' 
                            size='md'
                        />

                        <span className="w-fit rounded-full bg-tertiary px-3 py-2 text-xs font-bold text-tertiary-foreground">
                            {preguntas.length} / 15 preguntas
                        </span>
                    </div>
                    
                    <p className="mb-2 text-sm text-neutral-foreground">
                        Escribe el enunciado y sus opciones de respuesta.
                    </p>


                    <div className="flex flex-col gap-5">
                        {preguntas.map((pregunta, i) => (
                            <section
                                key={i}
                                className="flex flex-col gap-7 rounded-xl border border-border bg-white p-5 sm:p-6"
                            >
                                <div className='flex gap-3 flex-wrap justify-between items-center'>

                                    <div className='flex items-start md:items-center gap-3 md:flex-row flex-col md:w-[85%] w-[100%]'>
                                        <div className="flex items-center gap-3">
                                            <span className="grid size-9 shrink-0 place-items-center rounded-xl bg-tertiary text-sm font-bold text-primary">
                                                {String(i + 1).padStart(2, '0')}
                                            </span>
                                        </div>

                                        <Textarea
                                            value={pregunta.enunciado}
                                            onChange={(e) => {
                                                updatePregunta(i, { enunciado: e.target.value })
                                                limpiarErrorDe(`preguntasOpciones.${i}.enunciado`);
                                            }}
                                            rows={2}
                                            className={`w-full`}
                                            placeholder="Escribe aquí la pregunta"
                                        />
                                    </div>

                                    <button
                                        type="button" 
                                        onClick={() => deletePregunta(i)} 
                                        disabled={preguntas.length <= MIN_PREGUNTAS}
                                        aria-label={`Eliminar pregunta ${i + 1}`}
                                        className="inline-flex h-fit items-center gap-2 rounded-lg py-2 px-3 text-xs 
                                        font-semibold text-neutral-foreground transition hover:bg-error 
                                        hover:text-error-foreground disabled:cursor-not-allowed disabled:opacity-40"
                                    >
                                        <Trash2 className="size-4" /> Eliminar
                                    </button>

                                    <InputError 
                                        className='!font-normal' 
                                        message={errorDe(`preguntasOpciones.${i}.enunciado`)} 
                                    />
                                </div>



                                <fieldset className='space-y-3 min-w-0'>
                                    <legend className="flex text-sm font-semibold flex-wrap gap-2 items-center">
                                        Opciones de respuesta <span className="text-error-foreground">*</span>
                                        <span className="text-xs font-normal text-neutral-foreground">
                                            Selecciona la opción correcta
                                        </span>
                                    </legend>

                                    <div className="flex flex-col gap-3">
                                        {pregunta.opciones.map((opcion, j) => (
                                            <div key={j} className='min-w-0 flex flex-col'>
                                                <div className={`flex items-center gap-3 rounded-xl border px-3 py-2.5 transition 
                                                ${opcion.es_correcta ? 'border-primary/50 bg-tertiary/50' : 'border-border bg-background'}`}>
                                                    <Input
                                                        type="radio"
                                                        id={`pregunta-${i}-option-${j}-correcta`}
                                                        name={`pregunta-${i}-correcta`}
                                                        checked={opcion.es_correcta}
                                                        onChange={() => marcarCorrecta(i,j)}
                                                        aria-label={`Marcar opción ${j + 1} como correcta para la pregunta ${i + 1}`}
                                                        className="size-4 shrink-0 accent-primary"
                                                    />


                                                    <Label htmlFor={`pregunta-${i}-opcion-${j}-texto`} className="sr-only">
                                                        Texto de la opción {j + 1}
                                                    </Label>
                                                    <Input
                                                        id={`pregunta-${i}-opcion-${j}-texto`}
                                                        value={opcion.contenido}
                                                        onChange={(e) => updateTextoOpcion(i, j, e.target.value)}
                                                        className="min-h-9 w-full min-w-0 flex-1 bg-transparent text-sm 
                                                        outline-none placeholder:text-neutral-foreground border-0"
                                                        placeholder={`Opción ${j + 1}`}
                                                    />


                                                    <span
                                                        className={`hidden rounded-full px-2.5 py-1 text-[11px] font-bold sm:inline-flex 
                                                        ${opcion.es_correcta
                                                            ? 'bg-primary text-primary-foreground'
                                                            : 'text-neutral-foreground'}
                                                        `}
                                                    >
                                                        {opcion.es_correcta && (<><Check className="mr-1 size-3.5" /> Correcta</>)}
                                                    </span>


                                                    <button
                                                        type="button"
                                                        onClick={() => deleteOpcion(i, j)}
                                                        disabled={pregunta.opciones.length <= MIN_OPCIONES}
                                                        aria-label={`Eliminar opción ${j + 1} de la pregunta ${i + 1}`}
                                                        className="grid size-8 shrink-0 place-items-center rounded-lg text-neutral-foreground transition hover:bg-red-50 hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-30"
                                                    >
                                                        <Trash2 className="size-4" />
                                                    </button>
                                                </div>

                                                <InputError className='!font-normal break-words [overflow-wrap:anywhere]' message={errorDe(`preguntasOpciones.${i}.opciones.${j}.contenido`)} />
                                            </div>
                                        ))}
                                    </div>


                                    <button
                                        type="button"
                                        onClick={() => addOpcion(i)}
                                        disabled={pregunta.opciones.length >= MAX_OPCIONES}
                                        className="mt-3 inline-flex min-h-9 items-center gap-2 rounded-lg px-3 
                                        text-xs font-bold text-primary transition hover:bg-tertiary 
                                        disabled:cursor-not-allowed disabled:opacity-40"
                                    >
                                        <Plus className="size-4" /> Añadir opción{' '}
                                        <span className="font-normal text-neutral-foreground">
                                            ({pregunta.opciones.length}/{MAX_OPCIONES})
                                        </span>
                                    </button>

                                    <InputError className='!font-normal break-words [overflow-wrap:anywhere]' message={errorDe(`preguntasOpciones.${i}.opciones`)} />
                                </fieldset>
                            </section>
                        ))}
                    </div>

                    <button
                        type="button"
                        onClick={addPregunta}
                        disabled={preguntas.length >= MAX_PREGUNTAS}
                        className="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-dashed border-primary/40 bg-tertiary/40 px-4 py-3 text-sm font-bold text-primary transition hover:border-primary hover:bg-tertiary disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        <Plus className="size-4" /> Añadir pregunta{' '}
                        <span className="font-normal">({preguntas.length}/{MAX_PREGUNTAS})</span>
                    </button>

                    
                    <div className="mt-7 flex flex-col-reverse justify-end gap-3 border-t border-border pt-6 sm:flex-row">
                        <a
                            href={route('admin.panel')}
                            className="inline-flex min-h-11 items-center justify-center rounded-xl border border-border px-5 py-2.5 text-sm font-semibold text-foreground transition hover:border-primary hover:text-primary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-primary-foreground transition hover:opacity-90 disabled:opacity-50"
                        >
                            <Save className="size-4" /> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>            
        </AppLayout>
    )
}