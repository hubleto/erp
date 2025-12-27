import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormReceipt, { FormReceiptProps } from './FormReceipt';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import { FormProps } from '@hubleto/react-ui/core/Form';
import request from '@hubleto/react-ui/core/Request';

interface TableReceiptsProps extends HubletoTableProps {
}

interface TableReceiptsState extends HubletoTableState {
  tableContactsDescription?: any,
  tableLeadsDescription?: any,
  tableDealsDescription?: any,
  tableDocumentsDescription?: any,
}

export default class TableReceipts extends HubletoTable<TableReceiptsProps, TableReceiptsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Cashdesk/Models/Receipt',
  }

  props: TableReceiptsProps;
  state: TableReceiptsState;

  translationContext: string = 'Hubleto\\App\\Community\\Cashdesk\\Loader';
  translationContextInner: string = 'Components\\TableReceipts';

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide'
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/cashdesk/receipts/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps: FormReceiptProps = this.getFormProps() as FormReceiptProps;
    return <FormReceipt {...formProps}/>;
  }
}