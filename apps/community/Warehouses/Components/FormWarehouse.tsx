import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/src/core/Components/HubletoForm';
import Table, { TableProps, TableState } from 'adios/Table';

interface FormWarehouseProps extends HubletoFormProps { }
interface FormWarehouseState extends HubletoFormState { }

export default class FormWarehouse<P, S> extends HubletoForm<FormWarehouseProps, FormWarehouseState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Warehouses/Models/Team',
  }

  props: FormWarehouseProps;
  state: FormWarehouseState;

  translationContext: string = 'HubletoApp\\Community\\Warehouses::Components\\FormWarehouse';

  constructor(props: FormWarehouseProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
      <small>Warehouse</small>
    </>;
  }

  // renderContent(): JSX.Element {
  //   // This is an example code to render content of the form.
  //   // You should develop your own render content.
  //   return <>
  //     <div className='w-full flex gap-2'>
  //       <div className="p-4 flex-1 text-center">
  //         <i className="fas fa-users text-primary"></i>
  //       </div>
  //       <div className="flex-6">
  //         {this.inputWrapper('name')}
  //         {this.inputWrapper('color')}
  //         {this.inputWrapper('description')}
  //         {this.inputWrapper('id_manager')}
  //         {this.divider('Team members')}
  //         {this.state.id < 0 ?
  //           <div className="badge badge-info">First create team, then you will be prompted to add members.</div>
  //         :
  //           <Table
  //             uid='teams_members'
  //             model='HubletoApp/Community/Settings/Models/TeamMember'
  //             customEndpointParams={ { idTeam: this.state.id } }
  //           ></Table>
  //         }
  //       </div>
  //     </div>
  //   </>;
  // }
}
