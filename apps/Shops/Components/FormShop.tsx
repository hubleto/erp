import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

interface FormShopProps extends FormExtendedProps { }
interface FormShopState extends FormExtendedState { }

export default class FormShop<P, S> extends FormExtended<FormShopProps, FormShopState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-shop',
    model: 'Hubleto/App/Community/Shops/Models/Shop',
  }

  props: FormShopProps;
  state: FormShopState;

  translationContext: string = 'Hubleto\\App\\Community\\Shops\\Loader';
  translationContextInner: string = 'Components\\FormShop';

  refInputNewTodo: any;

  constructor(props: FormShopProps) {
    super(props);
    this.refInputNewTodo = React.createRef();
  }

  getStateFromProps(props: FormShopProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Shop')}</b> },
        ...this.getCustomTabs()
      ]
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  getRecordFormUrl(): string {
    return 'shops/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Shop')}</small>
      <h2>{this.state.record.address ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2 flex-col md:flex-row'>
            <div className='flex-1 border-r border-gray-100'>
              {this.inputWrapper('name', {cssClass: 'text-2xl'})}
              {this.inputWrapper('address', {cssClass: 'text-2xl'})}
              {this.inputWrapper('color')}
              {this.inputWrapper('short_description')}
              {this.inputWrapper('long_description')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('photo_1')}
              {this.inputWrapper('photo_2')}
              {this.inputWrapper('photo_3')}
              {this.inputWrapper('photo_4')}
              {this.inputWrapper('photo_5')}
            </div>
          </div>
        </>;
      break;

      default:
        super.renderTab(tabUid);
      break;
    }
  }

}
