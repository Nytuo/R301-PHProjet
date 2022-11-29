import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ProductComponent } from './product/product.component';
import {AdminComponent} from "./admin/admin.component";

const routes: Routes = [
  {path: 'admin', component: AdminComponent},
  {path: 'products', component: ProductComponent},
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
