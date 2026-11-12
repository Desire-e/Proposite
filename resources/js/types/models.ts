/**
 * Modelos que llegan del servidor y comparten varias páginas o componentes.
 * Los props propios de una sola página se quedan en esa página.
 */

export type UsuarioBasico = {
    id: string;
    name: string;
}

export type Oposicion = {
    id?: string;
    nombre: string;
    descripcion: string;
}

// ====== Foro ======

/** Publicación tal como llega al listado del foro */
export type PublicacionFeed = {
    id: string;
    contenido: string;
    created_at: string;
    usuario: UsuarioBasico;
    me_sirvio_count: number;
    comentarios_count: number;
    me_sirvio_activo: boolean;
}

export type Comentario = {
    id: string;
    usuario_id: string;
    publicacion_id: string;
    contenido: string;
    created_at: string;
    usuario: UsuarioBasico;
}

// ====== Temario ======

export type Recurso = {
    subido_por: string;
    temario_id: string;
    titulo: string;
    recurso_url: string;
}

export type Tema = {
    id: string;
    bloque: string | null;
    numero_tema: string;
    titulo: string;
    recursos?: Recurso[] | null;
    checked: boolean;
}

// ====== Tests ======

export type Test = {
    id: string;
    titulo: string;
    descripcion: string;
    numero_tema: string;
    preguntas_count: number;
    intentos_comunidad_count: number;
}

/** Test del listado, con la última nota del usuario (null si no lo ha hecho) */
export type TestListado = Test & {
    ultima_nota: number | null;
}
