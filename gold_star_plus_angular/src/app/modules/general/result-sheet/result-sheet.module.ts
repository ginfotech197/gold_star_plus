import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ResultSheetRoutingModule } from './result-sheet-routing.module';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import {ResultSheetComponent} from "../../../pages/result-sheet/result-sheet.component";
import {MatNativeDateModule} from "@angular/material/core";
import {MatButtonModule} from "@angular/material/button";
import {MatFormFieldModule} from "@angular/material/form-field";
import {MatDatepickerModule} from "@angular/material/datepicker";
import { ResultComponent } from '../../../pages/cpanel/result/result.component';





@NgModule({
  // declarations: [],
  imports: [
    CommonModule,
    ResultSheetRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    MatDatepickerModule,
    MatButtonModule,
    MatFormFieldModule,
    MatNativeDateModule,


  ],
  declarations: [
    ResultSheetComponent,
    //ResultComponent
  ],
  exports: [
    ResultSheetComponent,
    //ResultComponent
  ]

})
export class ResultSheetModule { }
