<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsersCreditsHistoryInTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->float('payee_previous_credits');
            $table->float('payee_current_credits');
            $table->float('payer_previous_credits');
            $table->float('payer_current_credits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payee_previous_credits',
                'payee_current_credits',
                'payer_previous_credits',
                'payer_current_credits'
            ]);
        });
    }
}
