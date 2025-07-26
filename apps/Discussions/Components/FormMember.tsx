import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormMemberProps extends HubletoFormProps { }
interface FormMemberState extends HubletoFormState { }

export default class FormMember<P, S> extends HubletoForm<FormMemberProps, FormMemberState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Discussions/Models/Team',
    tabs: {
      'default': { title: 'Task' },
      // Add your tabs here.
      // 'tab_with_nested_table': { title: 'Example tab with nested table' }
    }
  }

  props: FormMemberProps;
  state: FormMemberState;

  translationContext: string = 'HubletoApp\\Community\\Discussions::Components\\FormMember';

  constructor(props: FormMemberProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
      <small>Member</small>
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        return <>
         {/* <div className='w-full flex gap-2 flex-col md:flex-row'>
            <div className="flex-1">
              {this.inputWrapper('name')}
              {this.inputWrapper('id_type')}
              {this.inputWrapper('operational_status')}
              {this.inputWrapper('id_operational_manager')}
              {this.divider(this.translate('Contact'))}
              {this.inputWrapper('contact_person')}
              {this.inputWrapper('contact_email')}
              {this.inputWrapper('contact_phone')}
              {this.divider(this.translate('Capacity and occupancy'))}
              <div className="flex gap-2 flex-col">
                <div className="w-full">{this.inputWrapper('capacity')}</div>
                <div className="w-full">{this.inputWrapper('capacity_unit')}</div>
              </div>
              {this.inputWrapper('current_occupancy')}
            </div>
            <div className="flex-1 md:flex-row border-t md:border-l border-gray-200">
              {this.divider(this.translate('Address'))}
              {this.inputWrapper('address')}
              {this.inputWrapper('address_plus_code')}
              {this.inputWrapper('lng')}
              {this.inputWrapper('lat')}
              {this.divider(this.translate('More information'))}
              {this.inputWrapper('description')}
              {this.inputWrapper('photo_1')}
              {this.inputWrapper('photo_2')}
              {this.inputWrapper('photo_3')}
            </div>
          </div> */}
        </>;
      break;
      // case 'tab_with_nested_table':
      //   return (this.state.id < 0 ?
      //     <div className="badge badge-info">First create the master record, then the table will be rendered.</div>
      //   :
      //     <Table
      //       uid={this.props.uid + '_table_locations'}
      //       parentForm={this}
      //       model='HubletoApp/Community/Warehouses/Models/Location'
      //       customEndpointParams={ { idWarehouse: this.state.id } }
      //     ></Table>
      //   );
      // break;
    }
  }

}
