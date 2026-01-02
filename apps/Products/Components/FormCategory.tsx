import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableProducts from './TableProducts';

export interface FormCategoryProps extends HubletoFormProps {}
export interface FormCategoryState extends HubletoFormState {}

export default class FormCategory<P, S> extends HubletoForm<FormCategoryProps,FormCategoryState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Products/Models/Category',
  };

  props: FormCategoryProps;
  state: FormCategoryState;

  parentApp: string = 'Hubleto/App/Community/Products';

  translationContext: string = 'Hubleto\\App\\Community\\Products\\Loader';
  translationContextInner: string = 'Components\\FormCategory';

  constructor(props: FormCategoryProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props)
    };
  }

  getStateFromProps(props: FormCategoryProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Category')}</b> },
        { uid: 'description', title: this.translate('Description') },
        { uid: 'products', title: this.translate('Products') },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'products/categories/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Category')}</small>
      <h2>{this.state.record.title ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='grow'>
            {this.inputWrapper('name', {cssClass: 'text-2xl'})}
            {this.inputWrapper('id_parent')}
            {this.inputWrapper('color')}
            {this.inputWrapper('short_description')}
            {this.inputWrapper('url_slug')}
          </div>
          <div className='grow'>
            {this.inputWrapper('photo_1')}
            {this.inputWrapper('photo_2')}
            {this.inputWrapper('photo_3')}
            {this.inputWrapper('photo_4')}
            {this.inputWrapper('photo_5')}
          </div>
        </div>;
      break;
      case 'description':
        return <>
          {this.inputWrapper('long_description')}
        </>;
      break;
      case 'products':
        return (this.state.id < 0 ?
          <div className="badge badge-info">{this.translate("First create the category.")}</div>
        :
          <TableProducts
            uid={this.props.uid + "_table_category_products"}
            tag="table_category_products"
            parentForm={this}
            idCategory={R.id}
          />
        );
      break;
      default:
        super.renderTab(tabUid);
      break;
    }
  }

}