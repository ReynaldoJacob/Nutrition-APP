<?php

namespace App\Console\Commands;

use App\Models\Food;
use App\Services\UsdaFoodDataCentralService;
use Illuminate\Console\Command;

class BackfillFoodSpanishNames extends Command
{
    protected $signature = 'foods:backfill-es {--force : Sobrescribir name_es aunque ya exista}';

    protected $description = 'Genera/actualiza nombres en español para alimentos existentes.';

    public function handle(UsdaFoodDataCentralService $translator): int
    {
        $force = (bool) $this->option('force');

        $query = Food::query();
        if (! $force) {
            $query->whereNull('name_es');
        }

        $foods = $query->get();

        if ($foods->isEmpty()) {
            $this->info('No hay alimentos pendientes por traducir.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($foods->count());
        $bar->start();

        foreach ($foods as $food) {
            $food->name_es = $translator->translateLabelToSpanish($food->name);
            $food->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Traducción completada: ' . $foods->count() . ' alimentos actualizados.');

        return self::SUCCESS;
    }
}
