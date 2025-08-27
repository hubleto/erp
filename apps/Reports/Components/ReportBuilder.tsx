import React, { Component } from "react";
import request from "@hubleto/react-ui/core/Request";
import { ProgressBar } from 'primereact/progressbar';

import { Field, QueryBuilder, RuleGroupType, formatQuery, defaultOperators } from 'react-querybuilder';
import 'react-querybuilder/dist/query-builder.css';

export interface ReportBuilderProps {
  reportUrlSlug: string,
  model: string,
}

export interface ReportBuilderState {
  data: any,
  config: any,
  query: RuleGroupType,
}

export default class ReportBuilder extends Component<ReportBuilderProps,ReportBuilderState> {
  constructor(props) {
    super(props);

    this.state = {
      data: null,
      query: null,
      config: null,
    };
  }

  componentDidMount(): void {
    this.loadConfig();
  }

  loadConfig(): any {
    request.post(
      'reports/api/get-config',
      {model: this.props.model},
      {},
      (config: any) => {
        this.setState({config: config});
      }
    );
  }

  render(): JSX.Element {

    // const fields: Field[] = [
    //   { name: 'firstName', label: 'First Name' },
    //   { name: 'lastName', label: 'Last Name' },
    //   { name: 'age', label: 'Age', inputType: 'number' },
    //   { name: 'address', label: 'Address' },
    //   { name: 'phone', label: 'Phone' },
    //   { name: 'email', label: 'Email', validator: ({ value }) => /^[^@]+@[^@]+/.test(value) },
    //   { name: 'twitter', label: 'Twitter' },
    //   { name: 'isDev', label: 'Is a Developer?', valueEditorType: 'checkbox', defaultValue: false },
    // ];

    // const initialQuery: RuleGroupType = {
    //   combinator: 'and',
    //   rules: [],
    // };

    if (!this.state.config) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    const config = this.state.config;

    return <>
      <QueryBuilder
        fields={config.fields}
        defaultQuery={this.state.query}
        onQueryChange={(q: RuleGroupType) => {
          this.setState({query: q});
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
    </>;
  }
}
