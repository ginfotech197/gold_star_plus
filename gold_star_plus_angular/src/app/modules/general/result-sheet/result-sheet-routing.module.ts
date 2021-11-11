import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ResultSheetComponent } from '../../../pages/result-sheet/result-sheet.component';

const routes: Routes = [
  { path: '', component: ResultSheetComponent }

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ResultSheetRoutingModule { }
