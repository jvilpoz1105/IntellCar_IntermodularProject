<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('post')->insert([
            [
                'author_id' => 5, // Laura (prensa)
                'title' => 'Análisis: El nuevo Tesla Model 3 2024',
                'content' => 'Después de una semana probando el nuevo Model 3, puedo confirmar que Tesla ha mejorado notablemente la calidad interior. La suspensión es más confortable y el sistema de infoentretenimiento aún más rápido. La autonomía real ronda los 500 km en condiciones mixtas.',
                'model_id' => 16,
                'engine_id' => 16,
            ],
            [
                'author_id' => 2, // Carlos (pro)
                'title' => 'Mi experiencia pilotando en circuito',
                'content' => '¿Alguna vez habéis llevado un deportivo al límite en un circuito? Os puedo decir que no hay nada comparable. El agarre, la precisión en curva, el sonido del motor... una experiencia única. ¿Qué circuitos recomendáis en España?',
                'model_id' => null,
                'engine_id' => null,
            ],
            [
                'author_id' => 4, // Juan (tuning)
                'title' => 'Proyecto tuning: Golf GTI Stage 2',
                'content' => 'Acabo de terminar la repro Stage 2 en mi Golf GTI. Ahora saca 350 CV con downpipe, intercooler mejorado y admisión. ¡Una bestia! Os iré compartiendo vídeos del resultado.',
                'model_id' => 10,
                'engine_id' => 10,
            ],
            [
                'author_id' => 3, // María
                'title' => '¿Recomendaciones SUV eléctrico para familia?',
                'content' => 'Estoy pensando en cambiar mi coche por un SUV eléctrico. Tengo 2 niños pequeños y necesito algo espacioso. He visto el Tesla Model Y, Mercedes EQC y Audi Q4 e-tron. ¿Cuál recomendáis?',
                'model_id' => null,
                'engine_id' => null,
            ],
            [
                'author_id' => 5, // Laura (prensa)
                'title' => 'Porsche 911: El deportivo atemporal',
                'content' => 'He tenido el privilegio de probar el nuevo 911 Carrera S. Su diseño icónico sigue siendo fiel a sus raíces, pero con mejoras tecnológicas impresionantes. Los 450 CV del motor boxer se sienten perfectamente equilibrados. Sin duda, un sueño hecho realidad.',
                'model_id' => 20,
                'engine_id' => 20,
            ],
        ]);
    }
}
