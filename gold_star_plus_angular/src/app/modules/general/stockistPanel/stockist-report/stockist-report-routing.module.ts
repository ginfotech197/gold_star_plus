import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import {StockistReportComponent} from '../../../../pages/stockistPanel/stockist-report/stockist-report.component';

const routes: Routes = [
  // { path: '', canActivate : [AuthGuardStockistServiceService], component: StockistReportComponent }
  { path: '', component: StockistReportComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class StockistReportRoutingModule { }
