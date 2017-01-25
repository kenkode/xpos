<?php

class Payroll extends \Eloquent {
    public $table = "transact";

    /*

    use \Traits\Encryptable;


    protected $encryptable = [
        'basic_pay',
        'earning_amount',
        'taxable_income',
        'paye',
        'nssf_amount',
        'vol_amount',
        'nhif_amount',
        'net',
        'other_deductions',
        'total_deductions',
        'financial_month_year',

    ];
    */

public static $rules = [
        'period' => 'required',
        'account' => 'required'
    ];

    public static $messages = array(
        'period.required'=>'Please select period!',
        'account.required'=>'Please select account type!',
    );

    // Don't forget to fill this array
    protected $fillable = [];


    public function employees(){

        return $this->hasMany('Employee');
    }

    public static function allowances($id,$allowance_id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $allw = 0.00;
    
    $total_allws = DB::table('employee_allowances')
                     ->select(DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowances'))
                     ->where(function ($query) use ($id,$allowance_id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('allowance_id', '=', $allowance_id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$allowance_id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('allowance_id', '=', $allowance_id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
                      
    foreach($total_allws as $total_allw){
    $allw = $total_allw->total_allowances;
    }
    return round($allw,2);

    }

    public static function totalallowances($allowance_id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));
    
    $allw = DB::table('employee_allowances')
                     ->join('employee', 'employee_allowances.employee_id', '=', 'employee.id')
                     ->where('in_employment','Y')
                     ->where(function ($query) use ($allowance_id,$start){
                       $query->where('allowance_id', '=', $allowance_id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($allowance_id,$start) {
                        $query->where('allowance_id', '=', $allowance_id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->sum('allowance_amount');
                      
    
    return round($allw,2);

    }

    public static function allowanceall($id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $allw = 0.00;
    
    $total_allws = DB::table('employee_allowances')
                     ->select(DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowances'))
                     ->where(function ($query) use ($id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
                      
    foreach($total_allws as $total_allw){
    $allw = $total_allw->total_allowances;
    }
    return round($allw,2);

    }

    public static function nontaxables($id,$nontaxable_id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $nontax = 0.00;
    
    $total_nontaxes = DB::table('employeenontaxables')
                     ->select(DB::raw('COALESCE(sum(nontaxable_amount),0.00) as total_income'))
                     ->where(function ($query) use ($id,$nontaxable_id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('nontaxable_id', '=', $nontaxable_id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$nontaxable_id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('nontaxable_id', '=', $nontaxable_id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
                      
    foreach($total_nontaxes as $total_nontax){
    $nontax = $total_nontax->total_income;
    }
    return round($nontax,2);

    }

    public static function totalnontaxable($nontaxable_id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));
    
    $nontaxable = DB::table('employeenontaxables')
                     ->join('employee', 'employeenontaxables.employee_id', '=', 'employee.id')
                     ->where('in_employment','Y')
                     ->where(function ($query) use ($nontaxable_id,$start){
                       $query->where('nontaxable_id', '=', $nontaxable_id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($nontaxable_id,$start) {
                        $query->where('nontaxable_id', '=', $nontaxable_id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->sum('nontaxable_amount');
                      
    
    return round($nontaxable,2);

    }

    public static function nontaxableall($id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $nontax = 0.00;
    
    $total_nontaxes = DB::table('employeenontaxables')
                     ->select(DB::raw('COALESCE(sum(nontaxable_amount),0.00) as total_income'))
                     ->where(function ($query) use ($id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
                      
    foreach($total_nontaxes as $total_nontax){
    $nontax = $total_nontax->total_income;
    }
    return round($nontax,2);

    }

    public static function reliefs($id,$relief_id,$period){
    $rel = 0.00;
    
    $total_rels = DB::table('employee_relief')
                     ->select(DB::raw('COALESCE(sum(relief_amount),0.00) as total_reliefs'))
                     ->where('employee_id', '=', $id)
                     ->where('relief_id', '=', $relief_id)
                     ->get();
    foreach($total_rels as $total_rel){
    $rel = $total_rel->total_reliefs;
    }
    return round($rel,2);

    }

    public static function totalreliefs($relief_id,$period){
    
    $rel = DB::table('employee_relief')
                     ->join('employee', 'employee_relief.employee_id', '=', 'employee.id')
                     ->where('in_employment','Y')
                     ->where('relief_id', '=', $relief_id)
                     ->sum('relief_amount');
  
    return round($rel,2);

    }

    public static function reliefall($id,$period){
    $rel = 0.00;
    
    $total_rels = DB::table('employee_relief')
                     ->select(DB::raw('COALESCE(sum(relief_amount),0.00) as total_reliefs'))
                     ->where('employee_id', '=', $id)
                     ->get();
    foreach($total_rels as $total_rel){
    $rel = $total_rel->total_reliefs;
    }
    return round($rel,2);

    }

    public static function earnings($id,$earning_id,$period){
 
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $earn = 0.00;

    $total_earns = DB::table('earnings')
                     ->select(DB::raw('COALESCE(sum(earnings_amount),0.00) as total_earnings,instalments'))
                     ->where(function ($query) use ($id,$earning_id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('earning_id', '=', $earning_id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$earning_id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('earning_id', '=', $earning_id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
                      
    foreach($total_earns as $total_earn){
    if($total_earn->instalments>=1){
    $earn = $total_earn->total_earnings;
    }else{
    $earn = 0.00;
    }
    }
    
    return round($earn,2);

    }

    public static function totalearnings($earning_id,$period){
 
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

   $earn = 0.00;

    $total_earns = DB::table('earnings')
                     ->join('employee', 'earnings.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(earnings_amount),0.00) as total_earnings,instalments'))
                     ->where('in_employment','Y')
                     ->where(function ($query) use ($earning_id,$start){
                       $query->where('earning_id', '=', $earning_id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($earning_id,$start) {
                        $query->where('earning_id', '=', $earning_id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
                      
    foreach($total_earns as $total_earn){
    if($total_earn->instalments>=1){
    $earn = $total_earn->total_earnings;
    }else{
    $earn = 0.00;
    }
    }
    
    return round($earn,2);

    }

    public static function earningall($id,$period){
 
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $earn = 0.00;

    $total_earns = DB::table('earnings')
                     ->select(DB::raw('COALESCE(sum(earnings_amount),0.00) as total_earnings,instalments'))
                     ->where(function ($query) use ($id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
                      
    foreach($total_earns as $total_earn){
    if($total_earn->instalments>=1){
    $earn = $total_earn->total_earnings;
    }else{
    $earn = 0.00;
    }
    }
    
    return round($earn,2);

    }
    
    public static function salary_pay($id,$period){
    $salary = 0.00;
    
    $pays = DB::table('employee')
                     ->select(DB::raw('COALESCE(sum(basic_pay),0.00) as total_pay'))
                     ->where('id', '=', $id)
                     ->get();
    foreach($pays as $pay){
    $salary = $pay->total_pay;
    }
    return round($salary,2);
   }

   public static function total_pay(){
    $salary = DB::table('employee')->sum('basic_pay');
    
    return round($salary,2);
   }

   public static function overtimes($id,$overtime,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $otime = 0.00;
    
    $total_overtimes = DB::table('overtimes')
                     ->select(DB::raw('COALESCE(sum(amount*period),0.00) as overtimes'))
                     ->where(function ($query) use ($id,$overtime,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('type', '=', $overtime)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$overtime,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('type', '=', $overtime)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
    foreach($total_overtimes as $total_overtime){
    $otime = $total_overtime->overtimes;
    }
    return round($otime,2);

    }


    public static function totalovertimes($overtime,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $otime = DB::table('overtimes')
                     ->join('employee', 'overtimes.employee_id', '=', 'employee.id')
                     ->where('in_employment','Y')
                     ->where(function ($query) use ($overtime,$start){
                       $query->where('type', '=', $overtime)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($overtime,$start) {
                        $query->where('type', '=', $overtime)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->sum('amount*period');

    return round($otime,2);

    }

    public static function overtimeall($id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $otime = 0.00;
    
    $total_overtimes = DB::table('overtimes')
                     ->select(DB::raw('COALESCE(sum(amount*period),0.00) as overtimes'))
                     ->where(function ($query) use ($id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
    foreach($total_overtimes as $total_overtime){
    $otime = $total_overtime->overtimes;
    }
    return round($otime,2);

    }

    public static function total_benefits($id,$period){
    $total_earnings = 0.00;
    
    $total_earnings = static::allowanceall($id,$period)+static::earningall($id,$period)+static::overtimeall($id,$period);

    return round($total_earnings,2);

    }

    public static function gross($id,$period){
    $total_gross = 0.00;                  
    
    $total_gross = static::salary_pay($id,$period)+static::total_benefits($id,$period);

    return round($total_gross,2);

    }

    public static function totalgross($period){
    $total_gross = 0.00;

    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));
    
    $total_allw = DB::table('employee_allowances')
                     ->where(function ($query) use ($start){
                       $query->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($start) {
                        $query->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->sum('allowance_amount');

    $earn = DB::table('earnings')
                     ->where(function ($query) use ($start){
                       $query->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($start) {
                        $query->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->sum('earnings_amount');

      $otime = DB::table('overtimes')
                     ->where(function ($query) use ($start){
                       $query->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($start) {
                        $query->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->sum('amount*period');
    
    $total_gross = static::total_pay()+$total_allw+$earn+$otime;

    return round($total_gross,2);

    }


    public static function tax($id,$period){
    $paye = 0.00;
    $total_pay = static::gross($id,$period);
    $total_nssf = static::nssf($id,$period);
    $taxable = $total_pay-$total_nssf;
    $emps = DB::table('employee')->where('id', '=', $id)->get();
    foreach($emps as $emp){
    if($emp->income_tax_applicable=='0'){
    $paye=0.00;
    }else if($emp->income_tax_applicable=='1' && $emp->income_tax_relief_applicable=='1'){
    if($taxable>=11180 && $taxable<21715){
    $paye = 1118+($taxable-11180)*15/100;
    $paye = $paye-1280.00-static::reliefall($id,$period);
    }else if($taxable>=21715 && $taxable<32249){
    $paye = 2698.03+($taxable-21715)*20/100;
    $paye = $paye-1280.00-static::reliefall($id,$period);
    }else if($taxable>=32249 && $taxable<42783){
    $paye = 4804.73+($taxable-32249)*25/100;
    $paye = $paye-1280.00-static::reliefall($id,$period);
    }else if($taxable>=42783){
    $paye = 7438.11+($taxable-42783)*30/100;
    $paye = $paye-1280.00-static::reliefall($id,$period);
    }else{
    $paye = 0.00;
    }
    }else if($emp->income_tax_applicable=='1' && $emp->income_tax_relief_applicable=='0'){
    if($taxable>=11180 && $taxable<21715){
    $paye = 1118+($taxable-11180)*15/100;
    $paye = $paye-static::reliefall($id,$period);
    }else if($taxable>=21715 && $taxable<32249){
    $paye = 2698.03+($taxable-21715)*20/100;
    $paye = $paye-static::reliefall($id,$period);
    }else if($taxable>=32249 && $taxable<42783){
    $paye = 4804.73+($taxable-32249)*25/100;
    $paye = $paye-static::reliefall($id,$period);
    }else if($taxable>=42783){
    $paye = 7438.11+($taxable-42783)*30/100;
    $paye = $paye-static::reliefall($id,$period);
    }else{
    $paye = 0.00;
    }
    }else if($emp->income_tax_applicable=='0' && $emp->income_tax_relief_applicable=='1'){
     $paye = 0.00;
    }
    }
    if($paye<0){
     $paye = 0.00;
    }
    return round($paye,2);
   }


    public static function totaltax($id,$period){
    $paye = 0.00;
    $total_pay = static::gross($id,$period);
    $total_nssf = static::nssf($id,$period);
    $taxable = $total_pay-$total_nssf;
    $emps = DB::table('employee')->where('id', '=', $id)->get();
    foreach($emps as $emp){
    if($emp->income_tax_applicable=='0'){
    $paye=0.00;
    }else if($emp->income_tax_applicable=='1' && $emp->income_tax_relief_applicable=='1'){
    if($taxable>=11180 && $taxable<21715){
    $paye = 1118+($taxable-11180)*15/100;
    $paye = $paye;
    }else if($taxable>=21715 && $taxable<32249){
    $paye = 2698.03+($taxable-21715)*20/100;
    $paye = $paye;
    }else if($taxable>=32249 && $taxable<42783){
    $paye = 4804.73+($taxable-32249)*25/100;
    $paye = $paye;
    }else if($taxable>=42783){
    $paye = 7438.11+($taxable-42783)*30/100;
    $paye = $paye;
    }else{
    $paye = 0.00;
    }
    }else if($emp->income_tax_applicable=='1' && $emp->income_tax_relief_applicable=='0'){
    if($taxable>=11180 && $taxable<21715){
    $paye = 1118+($taxable-11180)*15/100;
    $paye = $paye;
    }else if($taxable>=21715 && $taxable<32249){
    $paye = 2698.03+($taxable-21715)*20/100;
    $paye = $paye;
    }else if($taxable>=32249 && $taxable<42783){
    $paye = 4804.73+($taxable-32249)*25/100;
    $paye = $paye;
    }else if($taxable>=42783){
    $paye = 7438.11+($taxable-42783)*30/100;
    $paye = $paye;
    }else{
    $paye = 0.00;
    }
    }else if($emp->income_tax_applicable=='0' && $emp->income_tax_relief_applicable=='1'){
     $paye = 0.00;
    }
    }
    if($paye<0){
     $paye = 0.00;
    }
    return round($paye,2);
   }

    public static function nssf($id,$period){
    $nssfAmt = 0.00;
    $total = static::gross($id,$period);
    $employee = Employee::find($id);
    if($employee->social_security_applicable=='0'){
    $nssfAmt = 0.00;
    }else{
    $nssf_amts = DB::table('social_security')->get();
    foreach($nssf_amts as $nssf_amt){
    $from=$nssf_amt->income_from;
    $to=$nssf_amt->income_to;
    if($total>=$from && $total<=$to){
    $nssfAmt=$nssf_amt->ss_amount_employee;
    }
    }
    }
    return round($nssfAmt,2);
   }

   public static function nhif($id,$period){
    $nhifAmt = 0.00;
    $total = static::gross($id,$period);
    $employee = Employee::find($id);
    if($employee->hospital_insurance_applicable=='0'){
    $nhifAmt = 0.00;
    }else{
    $nhif_amts = DB::table('hospital_insurance')->get();
    foreach($nhif_amts as $nhif_amt){
    $from=$nhif_amt->income_from;
    $to=$nhif_amt->income_to;
    if($total>=$from && $total<=$to){
    $nhifAmt=$nhif_amt->hi_amount;
    }
    }
    }
    return round($nhifAmt,2);
   }
    
    public static function deductions($id,$deduction_id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $other_ded = 0.00;

    
    $deds = DB::table('employee_deductions')
                     ->select(DB::raw('COALESCE(sum(deduction_amount),0.00) as total_deduction,instalments'))
                     ->where(function ($query) use ($id,$deduction_id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('deduction_id', '=', $deduction_id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$deduction_id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('deduction_id', '=', $deduction_id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
    foreach($deds as $ded){
    if($ded->instalments>=1){
    $other_ded = $ded->total_deduction;
    }else{
    $other_ded = 0.00;
    }
    }
    return round($other_ded,2);
   }

   public static function totaldeductions($deduction_id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $other_ded = 0.00;

   $deds = DB::table('employee_deductions')
                     ->join('employee', 'employee_deductions.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(deduction_amount),0.00) as total_deduction,instalments')) 
                     ->where('in_employment','Y')
                     ->where(function ($query) use ($deduction_id,$start){
                       $query->where('deduction_id', '=', $deduction_id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($deduction_id,$start) {
                        $query->where('deduction_id', '=', $deduction_id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
    foreach($deds as $ded){
    if($ded->instalments>=1){
    $other_ded = $ded->total_deduction;
    }else{
    $other_ded = 0.00;
    }
    }
    
    return round($other_ded,2);
   }

   public static function deductionall($id,$period){
    
    $part = explode("-", $period);
    $start = $part[1]."-".$part[0]."-01";
    $end  = date('Y-m-t', strtotime($start));

    $other_ded = 0.00;

    
    $deds = DB::table('employee_deductions')
                     ->select(DB::raw('COALESCE(sum(deduction_amount),0.00) as total_deduction,instalments'))
                     ->where(function ($query) use ($id,$start){
                       $query->where('employee_id', '=', $id)
                             ->where('formular', '=', 'Recurring')
                             ->where('first_day_month','<=',$start);
                       })
                      ->orWhere(function ($query) use ($id,$start) {
                        $query->where('employee_id', '=', $id)
                              ->where('instalments', '>', 0)
                              ->where('first_day_month','<=',$start)
                              ->where('last_day_month','>=',$start);
                        })->get();
    foreach($deds as $ded){
    if($ded->instalments>=1){
    $other_ded = $ded->total_deduction;
    }else{
    $other_ded = 0.00;
    }
    }
    return round($other_ded,2);
   }

   public static function total_deductions($id,$period){
    $total_deds = 0.00;
    
    $total_deds = static::tax($id,$period)+static::nssf($id,$period)+static::nhif($id,$period)+static::deductionall($id,$period);

    return round($total_deds,2);

    }

    public static function net($id,$period){
    $total_net = 0.00;
    
    $total_net = static::gross($id,$period)+static::nontaxableall($id,$period)-static::total_deductions($id,$period);

    return round($total_net,2);

    }

     public static function asMoney($value){

        return number_format($value, 2);

    }

    public static function processedsalaries($id,$period){

    $salary = 0.00;
    
    $pays = DB::table('transact')
                     ->select('employee_id',DB::raw('COALESCE(sum(basic_pay),0.00) as total_pay'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('employee_id')
                     ->get();

    foreach($pays as $pay){
    $salary = $pay->total_pay;
    }
    
    return  number_format($salary,2);

    }

    public static function processedgross($id,$period){

    $gross = 0.00;
    
    $pays = DB::table('transact')
                     ->select('employee_id',DB::raw('COALESCE(sum(taxable_income),0.00) as total_pay'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('employee_id')
                     ->first();

    $gross = $pays->total_pay;
    
    return  number_format($gross,2);

    }

    public static function processedhouseallowances($id,$period){

    $hallw = 0.00;
    
    $total_hallws = DB::table('transact_allowances')
                     ->select('employee_id',DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowances'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->where('employee_allowance_id', '=', 1)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_hallws as $total_hallw){
    $hallw = $total_hallw->total_allowances;
    }
    
    return  number_format($hallw,2);

    }

   public static function processedtransportallowances($id,$period){

    $tallw = 0.00;
    
    $total_tallws = DB::table('transact_allowances')
                     ->select('employee_id',DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowances'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->where('employee_allowance_id', '=', 2)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_tallws as $total_tallw){
    $tallw = $total_tallw->total_allowances;
    }
    
    return  number_format($tallw,2);

    }

    public static function processedotherallowances($id,$period){

    $oallw = 0.00;
    
    $total_oallws = DB::table('transact_allowances')
                     ->select('employee_id',DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowances'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->where('employee_allowance_id', '<>', 1)
                     ->where('employee_allowance_id', '<>', 2)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_oallws as $total_oallw){
    $oallw = $total_oallw->total_allowances;
    }
    
    return  number_format($oallw,2);

    }

    public static function processedreliefnames($id,$period){

    $rel = '';
    
    $total_rels = DB::table('transact_reliefs')
                     ->select('employee_id','relief_name')
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_rels as $total_rel){
    $rel = $total_rel->relief_name;
    }
    
    return $rel;

    }

    public static function processedreliefs($id,$period){

    $rel = 0.00;
    
    $total_rels = DB::table('transact_reliefs')
                     ->select('employee_id',DB::raw('COALESCE(sum(relief_amount),0.00) as total_reliefs'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_rels as $total_rel){
    $rel = $total_rel->total_reliefs;
    }
    
    return  number_format($rel,2);

    }

    public static function processedearningnames($id,$period){

    $earn = '';
    
    $total_earns = DB::table('transact_earnings')
        ->select('employee_id','earning_name')
        ->where('financial_month_year' ,'=', $period)
        ->where('employee_id' ,'=', $id)
        ->groupBy('employee_id')
        ->get();

    foreach($total_earns as $total_earn){
    $earn = $total_earn->earning_name;
    }
    
    return $earn;

    }

    public static function processedearnings($id,$period){

    $earn = 0.00;
    
    $total_earns = DB::table('transact_earnings')
        ->select('employee_id',DB::raw('COALESCE(sum(earning_amount),0.00) as total_earnings'))
        ->where('financial_month_year' ,'=', $period)
        ->where('employee_id' ,'=', $id)
        ->groupBy('employee_id')
        ->get();

    foreach($total_earns as $total_earn){
    $earn = $total_earn->total_earnings;
    }
    
    return  number_format($earn,2);

    }

    public static function processedovertimenames($id,$period){

    $otime = '';
    
    $total_overtimes = DB::table('transact_overtimes')
                     ->select('employee_id','overtime_type')
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id' ,'=', $id)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_overtimes as $total_overtime){
    $otime = $total_overtime->overtime_type;
    }
    
    return $otime;

    }

   public static function processedovertimes($id,$period){

    $otime = 0.00;
    
    $total_overtimes = DB::table('transact_overtimes')
                     ->select('employee_id',DB::raw('COALESCE(sum(overtime_period*overtime_amount),0.00) as overtimes'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id' ,'=', $id)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_overtimes as $total_overtime){
    $otime = $total_overtime->overtimes;
    }
    
    return  number_format($otime,2);

    }

    public static function processedpaye($id,$period){

    $tax = 0.00;
    
    $total_paye = DB::table('transact')
                     ->select('employee_id',DB::raw('COALESCE(sum(paye),0.00) as paye'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id' ,'=', $id)
                     ->groupBy('employee_id')
                     ->first();

    $tax = $total_paye->paye;

    return  number_format($tax,2);

    }

    public static function processedallowancenames($id,$period){

    $tallw = '';
    
    $total_tallws = DB::table('transact_allowances')
                     ->select('employee_id','allowance_name')
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('allowance_name')
                     ->get();

    foreach($total_tallws as $total_tallw){
    $tallw = $total_tallw->allowance_name;
    }
    
    return $tallw;

    }

    public static function processedallowances($id,$period){

    $tallw = 0.00;
    
    $total_tallws = DB::table('transact_allowances')
                     ->select('employee_id',DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowances'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('allowance_name')
                     ->get();

    foreach($total_tallws as $total_tallw){
    $tallw = $total_tallw->total_allowances;
    }
    
    return  number_format($tallw,2);

    }

    public static function processednontaxnames($id,$period){

    $tnontax= '';
    
    $total_nontaxes = DB::table('transact_nontaxables')
                     ->select('employee_id','nontaxable_name')
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('nontaxable_name')
                     ->get();

    foreach($total_nontaxes as $total_nontax){
    $tnontax = $total_nontax->nontaxable_name;
    }
    
    return $tnontax;

    }

    public static function processednontaxables($id,$period){

    $tnontax= 0.00;
    
    $total_nontaxes = DB::table('transact_nontaxables')
                     ->select('employee_id',DB::raw('COALESCE(sum(nontaxable_amount),0.00) as total_nontaxables'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('nontaxable_name')
                     ->get();

    foreach($total_nontaxes as $total_nontax){
    $tnontax = $total_nontax->total_nontaxables;
    }
    
    return  number_format($tnontax,2);

    }

    public static function processedNssf($id,$period){

    $nssf_amt = 0.00;
    
    $total_nssfs = DB::table('transact')
                     ->select('employee_id',DB::raw('COALESCE(sum(nssf_amount),0.00) as nssf'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_nssfs as $total_nssf){
    $nssf_amt = $total_nssf->nssf;
    }
    
    return  number_format($nssf_amt,2);

    }

     public static function processedNhif($id,$period){

    $nhif_amt = 0.00;
    
    $total_nhifs = DB::table('transact')
                     ->select('employee_id',DB::raw('COALESCE(sum(nhif_amount),0.00) as nhif'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('employee_id')
                     ->first();

    $nhif_amt = $total_nhifs->nhif;
    
    return  number_format($nhif_amt,2);

    }

     public static function processeddeductionnames($id,$period){

    $deductions = '';
    
    $total_deds= DB::table('transact_deductions')
                     ->select('employee_id','deduction_name')
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('deduction_name')
                     ->get();

    foreach($total_deds as $total_ded){
    $deductions = $total_ded->deduction_name;
    }
    
    return $deductions;

    }

    public static function processedDeductions($id,$period){

    $deductions = 0.00;
    
    $total_deds= DB::table('transact_deductions')
                     ->select('employee_id',DB::raw('COALESCE(sum(deduction_amount),0.00) as total_deductions'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                    ->groupBy('deduction_name')
                     ->get();

    foreach($total_deds as $total_ded){
    $deductions = $total_ded->total_deductions;
    }
    
    return  number_format($deductions,2);

    }

    public static function processedtotaldeds($id,$period){

    $deductions = 0.00;
    
    $total_deds= DB::table('transact')
                     ->select('employee_id',DB::raw('COALESCE(sum(total_deductions),0.00) as total_deductions'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_deds as $total_ded){
    $deductions = $total_ded->total_deductions;
    }
    
    return  number_format($deductions,2);

    }

    public static function processednet($id,$period){

    $net = 0.00;
    
    $total_nets= DB::table('transact')
                     ->select('employee_id',DB::raw('COALESCE(sum(net),0.00) as total_net'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('employee_id', '=', $id)
                     ->groupBy('employee_id')
                     ->get();

    foreach($total_nets as $total_net){
    $net = $total_net->total_net;
    }
    
    return  number_format($net,2);

    }

    public static function payecalc($gross){
    $paye = 0.00;
    $a = str_replace( ',', '', $gross);

    $total_pay = $a;
    $total_nssf = static::nssfcalc($gross);
    $taxable = $total_pay-$total_nssf;
    
    if($taxable>=11180 && $taxable<21715){
    $paye = (1118+($taxable-11180)*15/100)-1280;
    }else if($taxable>=21715 && $taxable<32249){
    $paye = (2698.03+($taxable-21715)*20/100)-1280;
    }else if($taxable>=32249 && $taxable<42783){
    $paye = (4804.73+($taxable-32249)*25/100)-1280;
    }else if($taxable>=42783){
    $paye = (7438.11+($taxable-42783)*30/100)-1280;
    }else{
    $paye = 0.00;
    }
    return round($paye,2);
   }

   public static function nssfcalc($gross){
    $nssfAmt = 0.00;
    $a = str_replace( ',', '', $gross);
    $total = $a;
    
    $nssf_amts = DB::table('social_security')->get();
    foreach($nssf_amts as $nssf_amt){
    $from=$nssf_amt->income_from;
    $to=$nssf_amt->income_to;
    if($total>=$from && $total<=$to){
    $nssfAmt=$nssf_amt->ss_amount_employee;
    }
    }
    return round($nssfAmt,2);
   }

   public static function nhifcalc($gross){
    $nhifAmt = 0.00;
    $a = str_replace( ',', '', $gross);
    $total = $a;
   
    $nhif_amts = DB::table('hospital_insurance')->get();
    foreach($nhif_amts as $nhif_amt){
    $from=$nhif_amt->income_from;
    $to=$nhif_amt->income_to;
    if($total>=$from && $total<=$to){
    $nhifAmt=$nhif_amt->hi_amount;
    }
   }
    return round($nhifAmt,2);
   }

    public static function netcalc($gross){
    $total_net = 0.00;
    
    $total_net = $gross-static::payecalc($gross)-static::nssfcalc($gross)-static::nhifcalc($gross);
    if($total_net<0){
     $total_net = 0;
    }else{
     $total_net = $gross-static::payecalc($gross)-static::nssfcalc($gross)-static::nhifcalc($gross);
    }

    return round($total_net,2);

    }

    public static function payencalc($net){
    $paye = 0.00;
    $a = str_replace( ',', '', $net);

    $total_pay = $a;
    $total_nssf = static::nssfncalc($net);
    $taxable = $total_pay-$total_nssf;
    
    if($taxable>=11180 && $taxable<21715){
    $paye = (1118+($taxable-11180)*15/100)-1280;
    }else if($taxable>=21715 && $taxable<32249){
    $paye = (2698.03+($taxable-21715)*20/100)-1280;
    }else if($taxable>=32249 && $taxable<42783){
    $paye = (4804.73+($taxable-32249)*25/100)-1280;
    }else if($taxable>=42783){
    $paye = (7438.11+($taxable-42783)*30/100)-1280;
    }else{
    $paye = 0.00;
    }
    return round($paye,2);
   }

   public static function nssfncalc($net){
    $nssfAmt = 0.00;
    $a = str_replace( ',', '', $net);
    $total = $a;
    
    $nssf_amts = DB::table('social_security')->get();
    foreach($nssf_amts as $nssf_amt){
    $from=$nssf_amt->income_from;
    $to=$nssf_amt->income_to;
    if($total>=$from && $total<=$to){
    $nssfAmt=$nssf_amt->ss_amount_employee;
    }
    }
    return round($nssfAmt,2);
   }

   public static function nhifncalc($net){
    $nhifAmt = 0.00;
    $a = str_replace( ',', '', $net);
    $total = $a;
   
    $nhif_amts = DB::table('hospital_insurance')->get();
    foreach($nhif_amts as $nhif_amt){
    $from=$nhif_amt->income_from;
    $to=$nhif_amt->income_to;
    if($total>=$from && $total<=$to){
    $nhifAmt=$nhif_amt->hi_amount;
    }
   }
    return round($nhifAmt,2);
   }

    public static function grosscalc($net){
      
        $total = 0;
        $gross = $net;
        $y =0 ;
        $x =0 ;
        
        for($i=$net;$i>0;$i--){
        
        $total = $net-static::payencalc($net)-static::nssfncalc($net)-static::nhifncalc($net);
      
        $gross=($gross-$total)+$net;
        $net=$total;
        $y=$x;
        $x=($gross-$net)/2;
        $i=$x-$y;
        }

    return round($gross,2);

    }

    public static function transactearnings($id,$name,$period){

    $earn = 0.00;

    $total_earns = DB::table('transact_earnings')
                  ->join('employee', 'transact_earnings.employee_id', '=', 'employee.id')
                  ->where('financial_month_year' ,'=', $period)
                  ->where('personal_file_number' ,'=', $id)
                  ->where('earning_name' ,'=', $name)
                  ->select(DB::raw('COALESCE(sum(earning_amount),0.00) as total_earnings'))
                  ->get();
                      
    foreach($total_earns as $total_earn){
    $earn = $total_earn->total_earnings;
    }
    
    return round($earn,2);

    }

    public static function transactovertimes($id,$name,$period){

    $otime = 0.00;

    $total_overtimes = DB::table('transact_overtimes')
                  ->join('employee', 'transact_overtimes.employee_id', '=', 'employee.id')
                  ->where('financial_month_year' ,'=', $period)
                  ->where('personal_file_number' ,'=', $id)
                  ->where('overtime_type' ,'=', $name)
                  ->select(DB::raw('COALESCE(sum(overtime_amount*overtime_period),0.00) as overtime_amount'))
                  ->get();
                      
    foreach($total_overtimes as $total_overtime){
    $otime = $total_overtime->overtime_amount;
    }
    
    return round($otime,2);

    }

    public static function transactallowances($id,$name,$period){

    $allw = 0.00;

    $total_allowances = DB::table('transact_allowances')
                  ->join('employee', 'transact_allowances.employee_id', '=', 'employee.id')
                  ->where('financial_month_year' ,'=', $period)
                  ->where('personal_file_number' ,'=', $id)
                  ->where('allowance_name' ,'=', $name)
                  ->select(DB::raw('COALESCE(sum(allowance_amount),0.00) as allowance_amount'))
                  ->get();
                      
    foreach($total_allowances as $total_allowance){
    $allw = $total_allowance->allowance_amount;
    }
    
    return round($allw,2);

    }

    public static function transactnontaxables($id,$name,$period){

    $nontax = 0.00;

    $total_nontaxs = DB::table('transact_nontaxables')
                  ->join('employee', 'transact_nontaxables.employee_id', '=', 'employee.id')
                  ->where('financial_month_year' ,'=', $period)
                  ->where('personal_file_number' ,'=', $id)
                  ->where('nontaxable_name' ,'=', $name)
                  ->select(DB::raw('COALESCE(sum(nontaxable_amount),0.00) as nontaxable_amount'))
                  ->get();
                      
    foreach($total_nontaxs as $total_nontax){
    $nontax = $total_nontax->nontaxable_amount;
    }
    
    return round($nontax,2);

    }

    public static function transactreliefs($id,$name,$period){

    $rel = 0.00;

    $total_rels = DB::table('transact_reliefs')
                  ->join('employee', 'transact_reliefs.employee_id', '=', 'employee.id')
                  ->where('financial_month_year' ,'=', $period)
                  ->where('personal_file_number' ,'=', $id)
                  ->where('relief_name' ,'=', $name)
                  ->select(DB::raw('COALESCE(sum(relief_amount),0.00) as relief_amount'))
                  ->get();
                      
    foreach($total_rels as $total_rel){
    $rel = $total_rel->relief_amount;
    }
    
    return round($rel,2);

    }

    public static function transactdeductions($id,$name,$period){

    $ded = 0.00;

    $total_deds = DB::table('transact_deductions')
                  ->join('employee', 'transact_deductions.employee_id', '=', 'employee.id')
                  ->where('financial_month_year' ,'=', $period)
                  ->where('personal_file_number' ,'=', $id)
                  ->where('deduction_name' ,'=', $name)
                  ->select(DB::raw('COALESCE(sum(deduction_amount),0.00) as deduction_amount'))
                  ->get();
                      
    foreach($total_deds as $total_ded){
    $ded = $total_ded->deduction_amount;
    }
    
    return round($ded,2);

    }

    public static function totaltransactearnings($name,$branch,$dept,$period){
 
   $earn = 0.00;

   $total_earns = array();

    if($branch == 'All' && $dept == 'All'){
    $total_earns = DB::table('transact_earnings')
                     ->join('employee', 'transact_earnings.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(earning_amount),0.00) as total_earnings'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('earning_name' ,'=', $name)
                     ->get();
    }else if($branch != 'All' && $dept == 'All'){
     $total_earns = DB::table('transact_earnings')
                     ->join('employee', 'transact_earnings.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(earning_amount),0.00) as total_earnings'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('earning_name' ,'=', $name)
                     ->get();
    }else if($branch == 'All' && $dept != 'All'){
     $total_earns = DB::table('transact_earnings')
                     ->join('employee', 'transact_earnings.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(earning_amount),0.00) as total_earnings'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('department_id' ,'=', $dept)
                     ->where('earning_name' ,'=', $name)
                     ->get();
    }else if($branch != 'All' && $dept != 'All'){
     $total_earns = DB::table('transact_earnings')
                     ->join('employee', 'transact_earnings.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(earning_amount),0.00) as total_earnings'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('department_id' ,'=', $dept)
                     ->where('earning_name' ,'=', $name)
                     ->get();
    }
                      
    foreach($total_earns as $total_earn){
    $earn = $total_earn->total_earnings;
    }
    
    return round($earn,2);

    }

    public static function totaltransactallowances($name,$branch,$dept,$period){
 
   $allowances = 0.00;

   $total_allowances = array();

    if($branch == 'All' && $dept == 'All'){
       $total_allowances = DB::table('transact_allowances')
                     ->join('employee', 'transact_allowances.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowance'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('allowance_name' ,'=', $name)
                     ->get();
    }else if($branch != 'All' && $dept == 'All'){
      $total_allowances = DB::table('transact_allowances')
                     ->join('employee', 'transact_allowances.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowance'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('allowance_name' ,'=', $name)
                     ->get();
    }else if($branch == 'All' && $dept != 'All'){
      $total_allowances = DB::table('transact_allowances')
                     ->join('employee', 'transact_allowances.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowance'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('department_id' ,'=', $dept)
                     ->where('allowance_name' ,'=', $name)
                     ->get();
    }else if($branch != 'All' && $dept != 'All'){
      $total_allowances = DB::table('transact_allowances')
                     ->join('employee', 'transact_allowances.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(allowance_amount),0.00) as total_allowance'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('department_id' ,'=', $dept)
                     ->where('allowance_name' ,'=', $name)
                     ->get();
    }

                      
    foreach($total_allowances as $total_allowance){
    $allowances = $total_allowance->total_allowance;
    }
    
    return round($allowances,2);

    }

  public static function totaltransactnontaxables($name,$branch,$dept,$period){
 
   $nontax = 0.00;

    if($branch == 'All' && $dept == 'All'){
    $total_nontaxables = DB::table('transact_nontaxables')
                     ->join('employee', 'transact_nontaxables.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(nontaxable_amount),0.00) as total_nontaxable'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('nontaxable_name' ,'=', $name)
                     ->get();
     }else if($branch != 'All' && $dept == 'All'){
    $total_nontaxables = DB::table('transact_nontaxables')
                     ->join('employee', 'transact_nontaxables.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(nontaxable_amount),0.00) as total_nontaxable'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('nontaxable_name' ,'=', $name)
                     ->get();
     }else if($branch == 'All' && $dept != 'All'){
    $total_nontaxables = DB::table('transact_nontaxables')
                     ->join('employee', 'transact_nontaxables.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(nontaxable_amount),0.00) as total_nontaxable'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('department_id' ,'=', $dept)
                     ->where('nontaxable_name' ,'=', $name)
                     ->get();
     }else if($branch != 'All' && $dept != 'All'){
    $total_nontaxables = DB::table('transact_nontaxables')
                     ->join('employee', 'transact_nontaxables.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(nontaxable_amount),0.00) as total_nontaxable'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('department_id' ,'=', $dept)
                     ->where('nontaxable_name' ,'=', $name)
                     ->get();
     }
                      
    foreach($total_nontaxables as $total_nontaxable){
    $nontax = $total_nontaxable->total_nontaxable;
    }
    
    return round($nontax,2);

    }

  public static function totaltransactreliefs($name,$branch,$dept,$period){
 
   $relief = 0.00;


    if($branch == 'All' && $dept == 'All'){
    $total_reliefs = DB::table('transact_reliefs')
                     ->join('employee', 'transact_reliefs.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(relief_amount),0.00) as total_relief'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('relief_name' ,'=', $name)
                     ->get();
    }else if($branch != 'All' && $dept == 'All'){
     $total_reliefs = DB::table('transact_reliefs')
                     ->join('employee', 'transact_reliefs.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(relief_amount),0.00) as total_relief'))
                     ->where('branch_id' ,'=', $branch)
                     ->where('financial_month_year' ,'=', $period)
                     ->where('relief_name' ,'=', $name)
                     ->get();
    }else if($branch == 'All' && $dept != 'All'){
     $total_reliefs = DB::table('transact_reliefs')
                     ->join('employee', 'transact_reliefs.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(relief_amount),0.00) as total_relief'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('department_id' ,'=', $dept)
                     ->where('relief_name' ,'=', $name)
                     ->get();
    }else if($branch != 'All' && $dept != 'All'){
     $total_reliefs = DB::table('transact_reliefs')
                     ->join('employee', 'transact_reliefs.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(relief_amount),0.00) as total_relief'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('department_id' ,'=', $dept)
                     ->where('relief_name' ,'=', $name)
                     ->get();
    } 

                      
    foreach($total_reliefs as $total_relief){
    $relief = $total_relief->total_relief;
    }
    
    return round($relief,2);

    }

    public static function totaltransactdeductions($name,$branch,$dept,$period){
 
   $deduction = 0.00;

    if($branch == 'All' && $dept == 'All'){
    $total_deductions = DB::table('transact_deductions')
                     ->join('employee', 'transact_deductions.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(deduction_amount),0.00) as total_deduction'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('deduction_name' ,'=', $name)
                     ->get();
    }else if($branch != 'All' && $dept == 'All'){
    $total_deductions = DB::table('transact_deductions')
                     ->join('employee', 'transact_deductions.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(deduction_amount),0.00) as total_deduction'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('deduction_name' ,'=', $name)
                     ->get();
    }else if($branch == 'All' && $dept != 'All'){
    $total_deductions = DB::table('transact_deductions')
                     ->join('employee', 'transact_deductions.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(deduction_amount),0.00) as total_deduction'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('department_id' ,'=', $dept)
                     ->where('deduction_name' ,'=', $name)
                     ->get();
    }else if($branch != 'All' && $dept != 'All'){
    $total_deductions = DB::table('transact_deductions')
                     ->join('employee', 'transact_deductions.employee_id', '=', 'employee.id')
                     ->select(DB::raw('COALESCE(sum(deduction_amount),0.00) as total_deduction'))
                     ->where('financial_month_year' ,'=', $period)
                     ->where('branch_id' ,'=', $branch)
                     ->where('department_id' ,'=', $dept)
                     ->where('deduction_name' ,'=', $name)
                     ->get();
    }
                      
    foreach($total_deductions as $total_deduction){
    $deduction = $total_deduction->total_deduction;
    }
    
    return round($deduction,2);

    }

    public static function totaltransacttax($id,$period){
    $paye = 0.00;
    $data = DB::table('transact')
            ->join('employee', 'transact.employee_id', '=', 'employee.personal_file_number')
            ->where('financial_month_year' ,'=', $period)
            ->where('employee.id' ,'=', $id)
            ->first();
    $total_pay = $data->taxable_income;
    $total_nssf = static::nssf($id,$period);
    $taxable = $total_pay-$total_nssf;
    $emps = DB::table('employee')->where('id', '=', $id)->get();
    foreach($emps as $emp){
    if($emp->income_tax_applicable=='0'){
    $paye=0.00;
    }else if($emp->income_tax_applicable=='1' && $emp->income_tax_relief_applicable=='1'){
    if($taxable>=11180 && $taxable<21715){
    $paye = 1118+($taxable-11180)*15/100;
    $paye = $paye;
    }else if($taxable>=21715 && $taxable<32249){
    $paye = 2698.03+($taxable-21715)*20/100;
    $paye = $paye;
    }else if($taxable>=32249 && $taxable<42783){
    $paye = 4804.73+($taxable-32249)*25/100;
    $paye = $paye;
    }else if($taxable>=42783){
    $paye = 7438.11+($taxable-42783)*30/100;
    $paye = $paye;
    }else{
    $paye = 0.00;
    }
    }else if($emp->income_tax_applicable=='1' && $emp->income_tax_relief_applicable=='0'){
    if($taxable>=11180 && $taxable<21715){
    $paye = 1118+($taxable-11180)*15/100;
    $paye = $paye;
    }else if($taxable>=21715 && $taxable<32249){
    $paye = 2698.03+($taxable-21715)*20/100;
    $paye = $paye;
    }else if($taxable>=32249 && $taxable<42783){
    $paye = 4804.73+($taxable-32249)*25/100;
    $paye = $paye;
    }else if($taxable>=42783){
    $paye = 7438.11+($taxable-42783)*30/100;
    $paye = $paye;
    }else{
    $paye = 0.00;
    }
    }else if($emp->income_tax_applicable=='0' && $emp->income_tax_relief_applicable=='1'){
     $paye = 0.00;
    }
    }
    if($paye<0){
     $paye = 0.00;
    }
    return round($paye,2);
   }


}