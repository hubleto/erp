import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormCategory from './FormCategory';

interface TableCategoriesProps extends TableExtendedProps {}

interface TableCategoriesState extends TableExtendedState {}

export default class TableCategories extends TableExtended<TableCategoriesProps, TableCategoriesState> {

  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Products/Models/Category',
  }

  props: TableCategoriesProps;
  state: TableCategoriesState;

  translationContext: string = 'Hubleto\\App\\Community\\Products\\Loader';
  translationContextInner: string = 'Components\\TableCategories';

  constructor(props: TableCategoriesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCategoriesProps) {
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
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/products/categories/' + (id > 0 ? id : 'add'));
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    return elements;
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormCategory {...formProps}/>;
  }
}