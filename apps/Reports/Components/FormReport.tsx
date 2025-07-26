import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import { Field, QueryBuilder, RuleGroupType, formatQuery, defaultOperators } from 'react-querybuilder';

import 'react-querybuilder/dist/query-builder.css';
import { parseJsonLogic } from "react-querybuilder/parseJsonLogic"

interface FormReportProps extends HubletoFormProps { }
interface FormReportState extends HubletoFormState { }

export default class FormReport<P, S> extends HubletoForm<FormReportProps, FormReportState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Reports/Models/Team',
  }

  props: FormReportProps;
  state: FormReportState;

  translationContext: string = 'HubletoApp\\Community\\Reports::Components\\FormReport';

  constructor(props: FormReportProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
      <small>Report</small>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    console.log(R, R._QUERY_BUILDER.fields);
    return <>
      <div className='w-full flex gap-2'>
        <div className="flex-1 border-r border-gray-100">
          {this.inputWrapper('title')}
          {this.inputWrapper('model')}
        </div>
        <div className="flex-1">
          {this.inputWrapper('notes')}
        </div>
      </div>
      {this.divider('Query')}
      <div className='w-full flex gap-2'>
        <div className="flex-1 border-r border-gray-100">
          <QueryBuilder
            fields={R._QUERY_BUILDER.fields}
            defaultQuery={parseJsonLogic(R.query)}
            onQueryChange={(q: RuleGroupType) => {
              // this.setState({query: q});
              this.updateRecord({query: JSON.stringify(formatQuery(q, { format: 'jsonlogic' }))});
              console.log(formatQuery(q, {
                format: 'natural_language',
                parseNumbers: true,
                getOperators: () => defaultOperators,
                fields: [
                  { value: 'firstName', label: 'First Name' },
                  { value: 'lastName', label: 'Last Name' },
                  { value: 'age', label: 'Age' },
                ],
              }));
            }}
          />
        </div>
        <div className="flex-1">
          {this.inputWrapper('query', {readonly: true})}
        </div>
      </div>
    </>;
  }
}
