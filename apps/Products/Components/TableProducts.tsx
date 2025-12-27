import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormProduct from './FormProduct';

interface TableProductsProps extends HubletoTableProps {
  idCategory?: number,
}

interface TableProductsState extends HubletoTableState {
  idCategory?: number,
}

export default class TableProducts extends HubletoTable<TableProductsProps, TableProductsState> {

  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Products/Models/Product',
  }

  props: TableProductsProps;
  state: TableProductsState;

  translationContext: string = 'Hubleto\\App\\Community\\Products\\Loader';
  translationContextInner: string = 'Components\\TableProducts';

  constructor(props: TableProductsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableProductsProps) {
    return {
      ...super.getStateFromProps(props)
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idCategory: this.props.idCategory,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/products/' + (id > 0 ? id : 'add'));
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    return elements;
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormProduct {...formProps}/>;
  }
}