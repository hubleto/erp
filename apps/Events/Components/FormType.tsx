import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormTypeProps extends HubletoFormProps { }
interface FormTypeState extends HubletoFormState { }

export default class FormType<P, S> extends HubletoForm<FormTypeProps, FormTypeState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Events/Models/Team',
  }

  props: FormTypeProps;
  state: FormTypeState;

  translationContext: string = 'HubletoApp\\Community\\Events::Components\\FormType';

  constructor(props: FormTypeProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
      <small>Type</small>
    </>;
  }

  // renderContent(): JSX.Element {
  //   // This is an example code to render content of the form.
  //   // You should develop your own render content.
  //   return <>
  //     <div className='w-full flex gap-2'>
  //       <div className="flex-1 border-r border-gray-100">
  //         {this.inputWrapper('name')}
  //         {this.inputWrapper('id_type')}
  //         {this.inputWrapper('operational_status')}
  //         {this.inputWrapper('id_operational_manager')}
  //         {this.divider(this.translate('Contact'))}
  //         {this.inputWrapper('contact_person')}
  //         {this.inputWrapper('contact_email')}
  //         {this.inputWrapper('contact_phone')}
  //         {this.divider(this.translate('Capacity and occupancy'))}
  //         <div className="flex gap-2">
  //           <div className="w-full">{this.inputWrapper('capacity')}</div>
  //           <div className="w-full">{this.inputWrapper('capacity_unit')}</div>
  //         </div>
  //         {this.inputWrapper('current_occupancy')}
  //       </div>
  //       <div className="flex-1">
  //         {this.divider(this.translate('Address'))}
  //         {this.inputWrapper('address')}
  //         {this.inputWrapper('address_plus_code')}
  //         {this.inputWrapper('lng')}
  //         {this.inputWrapper('lat')}
  //         {this.divider(this.translate('More information'))}
  //         {this.inputWrapper('description')}
  //         {this.inputWrapper('photo_1')}
  //         {this.inputWrapper('photo_2')}
  //         {this.inputWrapper('photo_3')}
  //       </div>
  //     </div>
  //     <div className="card mt-2">
  //       <div className="card-header">{this.translate('Locations')}</div>
  //       <div className="card-body">
  //         {this.state.id < 0 ?
  //           <div className="badge badge-info">First create warehouse, then you will be prompted to add its locations.</div>
  //         :
  //           <Table
  //             uid={this.props.uid + '_table_locations'}
  //             parentForm={this}
  //             model='HubletoApp/Community/Warehouses/Models/Location'
  //             customEndpointParams={ { idWarehouse: this.state.id } }
  //           ></Table>
  //         }
  //       </div>
  //     </div>
  //   </>;
  // }
}
